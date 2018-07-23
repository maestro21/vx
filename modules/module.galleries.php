<?php
class galleries extends masterdb  {



    function tables(){
        return
            [
                'galleries' => [
                    'fields' => $this->fields(),
                ],
                'galleries_images' => [
                    'fields' => [
                        'gal_id'    => [ WIDGET_HIDDEN, 'dbtype' => DB_INT, 'index' => TRUE ],
                        'name'      => [ WIDGET_ARRAY ],
                        'fname'     => [ WIDGET_FILE ],
                        'crdate'    => [ WIDGET_HIDDEN, 'dbtype' => DB_DATE ],

                    ],
                    'fk' => [
                        'gal_id' => 'galleries(id)'
                    ],
                ],
            ];
    }

    function fields() {
        return [
            'name'	=> 	[  WIDGET_TEXT, 'search' => TRUE, 'required' => TRUE ],
            'slug'  =>  [  WIDGET_SLUG, 'search' => TRUE, 'required' => TRUE ],
            'settings' => [ DB_ARRAY,    WIDGET_ARRAY,
                'children' => [
                    'filename'  => [ WIDGET_TEXT, 'default' => 'gfx/{class}/{slug}/{datetime}_{uid}.{ext}' ],
                    'thumb'     => [  WIDGET_TEXT, 'default' => 'gfx/{class}/{slug}/thumb_{datetime}_{uid}.{ext}' ],
                    'imgsize'   => [ WIDGET_SIZE ],
                    'thumbsize' => [ WIDGET_SIZE ],
                ],
            ],
            'cover' =>  [ WIDGET_HIDDEN, 'dbtype' => DB_INT ],
            'crdate' => [ WIDGET_HIDDEN, 'dbtype' => DB_DATE ],

        ];
    }

    function view($id = NULL) {
        $id =  v($id, $this->id());

        $gal = q($this->cl())->qget($id)->run();
        $images = q('galleries_images')
                    ->qlist('*', 0, 100)
                    ->where(qeq('gal_id', $id))
                    ->run();
        
        return [
            'name' => $gal['name'],
            'slug' => $gal['slug'],
            'img' => $images
        ];
    }
    
    function slider($id = NULL) {
        return $this->view($id);
    }

    function admin($id = NULL) {
        if(!empty($this->path[1]) && $this->path[1] != 'admin') {
            $this->tpl = 'view';
            $slug = $this->path[1];
            $this->id = q()
                ->select('id')
                ->from($this->cl())
                ->where("slug='$slug'")
                ->run(DBCELL);

            return $this->view();
        }

        return parent::admin();
    }

    function extend() {
        $this->perpage = 1000;
        $this->defmethod = 'admin';
    }

    function cache($data = [], $cl = '')  {
        parent::cache();
        $gals = cache('galleries');
        $data = $thumbs = [];
        $_data = q('galleries_images')->qlist()->run();
        foreach($_data as $row) {
            $data[$row['id']] = $gals[$row['gal_id']]['slug'] .'/'. $row['fname'];
            $thumbs[$row['id']] = $gals[$row['gal_id']]['slug'] . '/' . THUMB_PREFIX . $row['fname'];
        }
		cache('galleries_images', $data);
        cache('galleries_thumbs', $thumbs);
    }
    
    function save($row = null)  {
        $ret = parent:: save($row = null);
        $this->cache();
        return $ret;
    }

    function upimg() {
        $gid = $this->post['id'];
        $slug = $this->post['slug'];


        // gfx/{class}/{slug}/{datetime}_{uid}.{ext}
       $pol = [
           '{class}' => $this->cl(),
           '{slug}' => $slug
       ];
		// gfx/{class}/{slug}/{datetime}_{uid}.{ext}
        $gal = q($this->cl())->qget($gid)->run(DBROW);
        $fp = unserialize($gal['settings']);
        $fp[0] = $fp['filename'];
        $this->fileNamePolicy =  [ '/galnewfile/' => $fp ];

        $this->saveFiles($pol);
        //print_r($this->post);
        q('galleries_images')->qadd([
            'name' => pathinfo($this->files['galnewfile']['name'], PATHINFO_FILENAME),
            'gal_id' => $gid,
            'fname' => basename($this->post['galnewfile']['name'])
        ])->run();
        //$this->cache_gal($gid);
        $this->cache();
        return json_encode(array('redirect' => 'self', 'status' => 'ok', 'timeout' => 1));
    }

    function setmainpicture() {
        $gid = $this->path[2];
        $iid = $this->path[3];
        q($this->cl())->qedit(['cover' => $iid],qEq('id',$gid))->run();
    }

    function cache_gal($gid = null) {
        $_res = q('galleries_images')
                    ->qlist('id, fname')
                    ->where(qeq('gal_id', $gid))
                ->run();
        $data = [];
        foreach($_res as $row) {
            $data[$row['id']] = $row['fname'];
        }
		cache('gal_' . $gid, $data);
    }

    /** Delete element **/
    public function delimg($id = NULL) {
        $this->parse = P_JSON;
        $id = is_a($id,  $this->id());
        
        q('galleries_images')
            ->qdel($id)
            ->run();

        return [
            'redirect' => 'self', 
            'status' => 'ok', 
            'timeout' => 1
        ];
    }
}