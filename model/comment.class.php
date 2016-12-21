<?php
class Comment
{
    const   ID_NEW         = -1;  // For creating new, empty Comment object.
    const   NO_CHILDREN    =  0;  // Don't fetch the subcomments to display.
    const   FETCH_CHILDREN =  1;  // Fetch the subcomments to display.
    
    private $id;
    private $parentid;
    private $groupid;
    private $childtime;
    private $datetime;
    private $creator;
    private $content;
    private $cubecount;
    private $fullname;
    private $username;
    private $loggedIn;
    
    
    public function __construct($id=self::ID_NEW) {
        if ($id !== self::ID_NEW) {
            $id  = (int) $id;
            $sql = 'SELECT d.*,
                    FROM `discussion` AS d
                        JOIN `users` AS u ON u.`id` = d.`created_by`
                    WHERE d.`id` = $id
                    ORDER BY d.`datetime` ASC';
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            
            $c->id        = intval($row['id']);
            $c->parentid  = intval($row['parent']);
            $c->groupid   = intval($row['groupid']);
            $c->datetime  = intval($row['datetime']);
            $c->creator   = intval($row['created_by']);
            $c->content   = $row['content'];
            $c->cubecount = intval($row['cubecount']);
            $c->fullname  = "$row[firstname] $row[lastname]";
            $c->username  = $row['username'];
        }
        else {
            $this->id        = self::ID_NEW;
            $this->parentid  = -1;
            $this->groupid   = -1;
            $this->datetime  = time();
            $this->childtime = time();
            $this->creator   = -1;
            $this->content   = '';
            $this->cubecount = -1;
            $this->fullname  = null;
            $this->username  = null;
            $this->loggedIn = false;
        }
    }
    
    
    /* Create a comment object from the data in the given associative array.
     */
    public static function fromData($data, $isLoggedIn) {
        $c = new Comment(self::ID_NEW);
        
        // Transfer all the values into the comment's fields.
        $c->id       = intval($data['id']);
        $c->parentid = intval($data['parent']);
        $c->groupid  = intval($data['groupid']);
        $c->datetime = intval($data['datetime']);
        $c->creator  = intval($data['created_by']);
        $c->content  = $data['content'];
        $c->loggedIn = (boolean) $isLoggedIn;
        
        // Use the given cube count if possible, or else query it later.
        if (array_key_exists('cubecount', $data))
            $c->cubecount = intval($data['cubecount']);
        
        // Use the given first/last names if possible, or else query it later.
        if (array_key_exists('firstname', $data) and
            array_key_exists('lastname', $data))
            $c->fullname = "$data[firstname] $data[lastname]";
        
        return $c;
    }
    
    
    /* Simple setter methods */
    
    public function setParentId($parentid) {
        $this->parentid = (int) $parentid;
    }
    
    public function setGroupId($groupid) {
        $this->groupid = (int) $groupid;
    }
    
    public function setDateTime($datetime) {
        $this->datetime = (int) $datetime;
    }
    
    public function setCreatorId($creatorid) {
        $creatorid = (int) $creatorid;
        if ($this->creator !== $creatorid) {
            $this->creator  = $creatorid;
            $this->fullname = null;
            $this->username = null;
        }
    }
    
    public function setContent($content) {
        $this->content = (string) $content;
    }

    public function setLoggedIn($val) {
        $this->loggedIn = (boolean) $val;
    }
    
    
    /* Save a new comment's data into the database. Comments do not support
     * updates, so re-saving an existing comment is an error.
     */
    public function save() {
        if ($this->id === self::ID_NEW) {
            // Add this new comment object to the database.
            $sql = "INSERT INTO `discussion`
                    (`parent`, `groupid`, `datetime`, `childtime`, `cubeid`,
                     `created_by`, `content`)
                    VALUES ({$this->parentid},
                            {$this->groupid},
                            {$this->datetime},
                            {$this->childtime},
                            -1,
                            {$this->creator},
                            '".mysql_real_escape_string($this->content)."')";
            $res = mysql_query($sql);
            
            // Fetch its auto-incremented ID and store it off.
            $sql = 'SELECT LAST_INSERT_ID() FROM `discussion`';
            $res = mysql_query($sql);
            $row = mysql_fetch_row($res);
            $this->id = intval($row[0]);
            
            // If this has a parent, update its corresponding `childtime`.
            if ($this->parentid > 0) {
                $sql = "UPDATE `discussion` SET `childtime` = {$this->datetime}
                        WHERE `id` = {$this->parentid}";
                mysql_query($sql);
            }
        }
        else
            throw new Exception('Comments cannot be updated.');
    }
    
    
    /* Produce the HTML output to show this comment / subcomment. The parameter
     * `usechildren` determines, for top level comments, if we should query the
     * children to show as well, or only format the top level comment.
     */
    public function format($userid, $usechildren=self::NO_CHILDREN) {
        // Check if the given user has cubed this comment themselves.
        $cubed = $this->loggedIn ? false : $this->hasUserCubed($userid);
        
        // Show the delete link only if this was created by the given user.
        // Note: first check to see if user is logged in
        if ($this->loggedIn == true && $this->creator == $userid )
            $delete_block ='
                <p class="fr">
                  <a href="javascript:void(0);" class="delete-discussion"
                  id="delete-discussion-'.
                  ($this->parentid === -1 ? $this->id : $this->parentid).'-'.
                  $this->id.'">Delete</a>
                </p>';
        
        if ($this->loggedIn == true) {
            // Figure out the `Cube/Cubed(n)` and `Show Cubes` state.
            $cubing_block = '
                <p class="fl">
                  <a href="javascript:void(0);" class="cube-this'.
                    ($cubed ? ' hide' : '').'" id="cube-this-'.$this->id.
                    '-1">Cube (<span id="cube-this-num-'.$this->id.'">'.
                    $this->getCubeCount().'</span>)</a>
                  <a href="javascript:void(0);" class="cube-this-already'.
                    ($cubed ? '' : ' hide').'" id="cube-this-already-'.
                    $this->id.'-1">Cubed (<span id="cube-num-already-'.
                    $this->id.'">'.$this->getCubeCount().'</span>)</a>
                  | <a href="javascript:void(0);" class="view-cubes"
                      id="view-cubes-'.$this->id.'-1">View Cubes</a>
                </p>';
        } else {
            $cubing_block = '
                <p class="fl">
                  <a href="login.php">Cube ('.
                    $this->getCubeCount().')</a>
                  | <a href="javascript:void(0);" class="view-cubes"
                      id="view-cubes-'.$this->id.'-1">View Cubes</a>
                </p>';
        } 
        
        
        
        // Create the HTML structure for either a comment or subcomment.
        if ($this->parentid === -1) {
            // Determine what to do about child comments.
            $children = array();
            if ($usechildren === self::FETCH_CHILDREN) {
                $sql = "SELECT d.*,
                            u.`first_name` AS firstname,
                            u.`last_name`  AS lastname,
                            (SELECT COUNT(*) FROM `cubes` AS c
                             WHERE c.`contentid` = d.`id`
                               AND c.`type`      = 1) AS `cubecount`
                        FROM `discussion` AS d
                            JOIN `users` AS u ON u.`id` = d.`created_by`
                        WHERE d.`parent` = {$this->id}
                        ORDER BY d.`datetime` ASC";
                $res = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($res))
                    $children[] = self::fromData($row, $this->loggedIn);
            }
            
            $s = '
            <ul class="nested-comments nostyle" id="inner-comment-item-'.$this->id.'">
              <li>
                <div class="comment">
                  <p><a href="dashboard-messages.php?u='.$this->getUserName().'" class="author">'.
                    $this->getFullname().'</a>
                  </p>
                  <h5><i><abbr class="timeago" title="'.
                    date(DATE_ISO8601, $this->datetime).'">'.
                    date('d-m-Y H:i:s', $this->datetime).'</abbr></i>
                  </h5>
                  <p class="comment-paragraph">'.$this->content.'</p>
                  <div class="cf">'.$cubing_block.@$delete_block.'</div>
                </div>
                <ul class="nostyle">
                  <div id="sub-comments-'.$this->id.'">';
            foreach ($children as $child)
                $s .= $child->format($userid);
            $s .= '
                  </div>';
            if ($this->loggedIn) {
                $s.= '
                    <li class="inner">
                    <div class="comment">
                      <form action="javascript:void(0);" class="form-commentoncomment" id="form-commentoncomment-'.$this->id.'">
                        <fieldset>
                          <p>
                            <input type="text" class="full-size-input2 commentoncomment" id="commentoncomment-'.$this->id.'" placeholder="Reply..." />
                          </p>
                        </fieldset>
                      </form>
                    </div>
                  </li>';
            }
            $s.= '      
                </ul>
              </li>
            </ul>';
        }
        else {
            $s = '
            <li class="inner" id="inner-comment-item-'.$this->id.'">
              <div class="comment">
                <p><a href="dashboard-messages.php?u='.$this->getUserName().'" class="author">'.
                  $this->getFullname().'</a>
                </p>
                <h5><i><abbr class="timeago" title="'.
                  date(DATE_ISO8601, $this->datetime).'">'.
                  date('d-m-Y H:i:s', $this->datetime).'</abbr></i>
                </h5>
                <p class="comment-paragraph">'.$this->content.'</p>
                  <div class="cf">'.$cubing_block.@$delete_block.'</div>
              </div>
            </li>';
        }
        return $s;
    }
    
    
    /* Fetch the already-gotten cube count for this, or else query it.
     */
    private function getCubeCount() {
        if ($this->cubecount === -1 and $this->id != self::ID_NEW) {
            $sql = 'SELECT COUNT(*) FROM `cubes`
                    WHERE `contentid` = '.$this->id.'
                      AND `type`      = 1';
            $res = mysql_query($sql);
            $row = mysql_fetch_row($res);
            $this->cubecount = intval($row[0]);
        }
        return $this->cubecount;
    }
    
    
    /* Fetch the already-gotten full name of the creator, or else query it.
     */
    private function getFullname() {
        if ($this->fullname === null and $this->id != self::ID_NEW) {
            $sql = "SELECT `first_name`, `last_name` FROM `users`
                    WHERE `id` = {$this->creator}";
            $res = mysql_query($sql);
            $row = mysql_fetch_row($res);
            $this->fullname = "$row[0] $row[1]";
        }
        return $this->fullname;
    }
    private function getUserName() {
        if ($this->username === null and $this->id != self::ID_NEW) {
            $sql = "SELECT `username` FROM `users`
                    WHERE `id` = {$this->creator}";
            $res = mysql_query($sql);
            $row = mysql_fetch_row($res);
            $this->username = $row[0];
        }
        return $this->username;
    }
    
    
    /* Check if the given user has cubed this particular comment.
     */
    private function hasUserCubed($userid) {
        $userid = (int) $userid;
        $sql = "SELECT `id` FROM `cubes`
                WHERE `contentid` = {$this->id}
                  AND `userid`    = $userid
                  AND `type`      = 1";
        $res = mysql_query($sql);
        return ($res and mysql_num_rows($res) == 1);
    }
}
?>