<?php
/**
 * Smarty Plugin Compile Include If Exists
 *
 * Compiles the {include_if_exists} tag
 *
 * Inspired by Liu Song's similar plugin for Smarty v2
 * @see        http://code.google.com/p/smartyplugin-include-if-exists/
 *
 * @author     Attila Fulop <fulopattila122@gmail.com>
 * @version    1 2013-01-31
 * 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @example    {include_if_exists file="foo.tpl" else="default.tpl"}
 * 
 */

class Smarty_Compiler_Include_If_Exists extends Smarty_Internal_CompileBase {
   
   protected function _removeElse($args, $fileReplace = NULL)
   {
      $result = array();
      foreach ($args as $arg) {
         if (isset($fileReplace) && isset($arg['file'])) {
               $result[] = array('file' => $fileReplace);
         } elseif (!isset($arg['else'])) {
            $result[] = $arg; 
         }
      }
      return $result;
   }
   
   public function compile($args, $compiler) { 
      $this->compiler = $compiler; 
      $this->required_attributes = array('file'); 
      $this->optional_attributes = array('else'); 

      //check and get attributes 
      $_attr = $this->getAttributes($compiler, $args);
      $_file = isset($_attr['file']) ? trim($_attr['file'], "'\"") : '';
      $_else = isset($_attr['else']) ? trim($_attr['else'], "'\"") : '';
      
      if ($this->compiler->smarty->templateExists($_file)) {
         $args_out = $this->_removeElse($args);
      } elseif (!empty($_else) && $this->compiler->smarty->templateExists($_else)) {
         $args_out = $this->_removeElse($args, $_attr['else']);
      } else {
         return '';
      }
      
      $includer = new Smarty_Internal_Compile_Include();
      return $includer->compile($args_out, $compiler);
   } 
} 
