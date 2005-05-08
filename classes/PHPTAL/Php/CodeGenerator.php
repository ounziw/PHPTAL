<?php

require_once 'PHPTAL/Php/Node.php';
require_once 'PHPTAL/Php/State.php';
require_once 'PHPTAL/Php/CodeWriter.php';

class PHPTAL_Php_CodeGenerator
{
    public function __construct($sourcePath)
    {
        $this->_functionName = 'tpl_'.PHPTAL_VERSION.md5($sourcePath);
        $this->_sourceFile = $sourcePath;
        $this->_state = new PHPTAL_Php_State();
        $this->_writer = new PHPTAL_Php_CodeWriter($this->_state);
    }

    public function setOutputMode($mode)
    { 
        $this->_state->setOutputMode($mode);
    }
    
    public function setEncoding($enc)
    { 
        $this->_state->setEncoding($enc);
    }

    public function generate(PHPTAL_Dom_Tree $tree)
    {
        $treeGen = new PHPTAL_Php_NodeTree($this->_writer, $tree);

        $header = sprintf('Generated by PHPTAL from %s', $this->_sourceFile);
        $this->_writer->doFunction($this->_functionName, '$tpl, $ctx');
        $this->_writer->doComment($header);
        $this->_writer->setFunctionPrefix($this->_functionName . "_");
        $this->_writer->pushCode('ob_start()');
        $treeGen->generate();
        $this->_writer->pushCode('$_result_ = ob_get_contents()');
        $this->_writer->pushCode('ob_end_clean()');
        $this->_writer->pushCode('return $_result_');
        $this->_writer->doEnd();
    }

    public function getResult()
    {
        return $this->_writer->getResult();
    }

    private $_functionName;
    private $_sourceFile;
    private $_writer;
    private $_state;
}

?>
