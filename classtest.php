<?php
class class1 {
    function __construct($v1) {
        $this->var1 = $v1;
    }
    function test() {
        $this->var1 = "bruh";
    }
}

class class2 {
    function __construct($v1) {
        $this->var2 = $v1;
        $class1 = new class1($this->var2);
        $class1->test();
        echo $class1->var1;
    }
}

$loadclass = new class2("Test");
?>