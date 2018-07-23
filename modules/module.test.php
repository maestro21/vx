<?php
val('TEST_DB',1);

if(val('TEST_DB')) {
    class test extends masterdb   {
        use generate;
    }
} else {
    class test extends mastercache  {
        use generate;
    }
}


function randw($length=8){
    return substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"),0,$length);
}


trait generate {
    

    public function extend() {
        $this->buttons['items']['addrows'] = 'fa-list';
    }

    public function fields() {
        $widgets =  widgets();
        $options = [ WIDGET_CHECKBOXES, WIDGET_SELECT, WIDGET_RADIO, WIDGET_MULTSELECT];
        $ret = [];
        foreach($widgets as $widget) {
            switch($widget) {
                case WIDGET_ARRAY:
                case WIDGET_TABLE:
                case WIDGET_KEYVALUES:
                    continue;
                    break;
            }
            $ret[$widget] = [
                $widget,
            ];
            if(in_array($widget, $options)) {
                $ret[$widget]['options'] = range(1,10);
            }
        }
        return $ret;
    }

    public function addrows($def = 100) {
        for($i = 0; $i < $def; $i++) {
            $row = [];
            foreach($j = $this->fields() as $k => $v) {
                $row[$k] = rand(0, 99999) . randw();
            }
            $this->save($row);
        }
    }

    public function install() {
        if(val('TEST_DB')) {
            install($this->tables());
        }
        $this->addrows();
    }
}

