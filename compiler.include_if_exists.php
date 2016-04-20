<?php
/**
 * Smarty Plugin Compile Include If Exists for Smarty v3
 *
 * Compiles the {include_if_exists} tag
 *
 * Inspired by Liu Song's similar plugin for Smarty v2
 * @see        http://code.google.com/p/smartyplugin-include-if-exists/
 *
 * @author     Attila Fulop
 * @version    2016-04-20
 * 
 * @license    MIT
 * @example    {include_if_exists file="foo.tpl" else="default.tpl"}
 * 
 */

class Smarty_Compiler_Include_If_Exists extends Smarty_Internal_CompileBase {
   
   public function compile($args, $compiler) { 
      $this->compiler = $compiler; 
      $this->required_attributes = array('file');
      $this->optional_attributes = array('else');

      //check and get attributes 
      $_attr = $this->getAttributes($compiler, $args);
      
      $include_file = $_attr['file'];
      $include_else = isset($_attr['else']) ? $_attr['else'] : null;
      
      $_parent_scope = Smarty::SCOPE_LOCAL;
      if (isset($_attr['scope'])) {
         $_attr['scope'] = trim($_attr['scope'], "'\"");
         if ($_attr['scope'] == 'parent') {
            $_parent_scope = Smarty::SCOPE_PARENT;
         } elseif ($_attr['scope'] == 'root') {
            $_parent_scope = Smarty::SCOPE_ROOT;
         } elseif ($_attr['scope'] == 'global') {
            $_parent_scope = Smarty::SCOPE_GLOBAL;
         }
      }
      
      $_caching = 'null';
      if ($compiler->nocache || $compiler->tag_nocache) {
         $_caching = Smarty::CACHING_OFF;
      }
      // default for included templates
      if ($compiler->template->caching && !$compiler->nocache && !$compiler->tag_nocache) {
         $_caching = self::CACHING_NOCACHE_CODE;
      }
      
      if (isset($_attr['cache_lifetime'])) {
         $_cache_lifetime = $_attr['cache_lifetime'];
         $compiler->tag_nocache = true;
         $_caching = Smarty::CACHING_LIFETIME_CURRENT;
      } else {
         $_cache_lifetime = 'null';
      }
      
      if (isset($_attr['cache_id'])) {
         $_cache_id = $_attr['cache_id'];
         $compiler->tag_nocache = true;
         $_caching = Smarty::CACHING_LIFETIME_CURRENT;
      } else {
         $_cache_id = '$_smarty_tpl->cache_id';
      }
      
      if (isset($_attr['compile_id'])) {
         $_compile_id = $_attr['compile_id'];
      } else {
         $_compile_id = '$_smarty_tpl->compile_id';
      }
      
      // delete {include} standard attributes
      unset($_attr['file'], $_attr['else'], $_attr['assign'], $_attr['cache_id'], $_attr['compile_id'], $_attr['cache_lifetime'], $_attr['nocache'], $_attr['caching'], $_attr['scope'], $_attr['inline']);
      // remaining attributes must be assigned as smarty variable
      if (!empty($_attr)) {
         if ($_parent_scope == Smarty::SCOPE_LOCAL) {
            // create variables
            foreach ($_attr as $key => $value) {
               $_pairs[] = "'$key'=>$value";
            }
               $_vars = 'array('.join(',',$_pairs).')';
               $_has_vars = true;
            } else {
               $compiler->trigger_template_error('variable passing not allowed in parent/global scope', $compiler->lex->taglineno);
            }
      } else {
         $_vars = 'array()';
         $_has_vars = false;
      }

      $output = '<?php if ($_smarty_tpl->templateExists(' . $include_file . ")) {\n"
         . "\t\t echo \$_smarty_tpl->_subTemplateRender(" . $include_file
         . ", $_cache_id, $_compile_id, $_caching, $_cache_lifetime, $_vars, $_parent_scope);\n";
      if (isset($include_else)) {
         $output .= "\t} elseif (\$_smarty_tpl->templateExists($include_else)) {\n"
         . "\t\t echo \$_smarty_tpl->_subTemplateRender(" . $include_else
         . ", $_cache_id, $_compile_id, $_caching, $_cache_lifetime, $_vars, $_parent_scope);\n";
      }
      $output .= "\t}\n?>\n";
      
      return $output;
   } 
} 
