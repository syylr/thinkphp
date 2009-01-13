<?php
class MyClass
{
    public function test($arg = null)
    {
        if (null === $arg) {
            return "MyClass.test()";
        } else {
            return $arg;
        }
    }
}
?>