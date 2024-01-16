<?php

namespace Elemizer\App\Components;

class HtmlInspector
{
   const HTML_ELEMENT_NAMES = [
      'a',
      'abbr',
      'acronym',
      'address',
      'applet',
      'area',
      'article',
      'aside',
      'audio',
      'b',
      'base',
      'basefont',
      'bdi',
      'bdo',
      'blockquote',
      'body',
      'br',
      'button',
      'canvas',
      'caption',
      'center',
      'cite',
      'code',
      'col',
      'colgroup',
      'data',
      'datalist',
      'dd',
      'del',
      'details',
      'dfn',
      'dialog',
      'dir',
      'div',
      'dl',
      'dt',
      'em',
      'embed',
      'fieldset',
      'figcaption',
      'figure',
      'font',
      'footer',
      'form',
      'frame',
      'frameset',
      'h1',
      'h2',
      'h3',
      'h4',
      'h5',
      'h6',
      'head',
      'header',
      'hr',
      'html',
      'i',
      'iframe',
      'img',
      'input',
      'ins',
      'kbd',
      'label',
      'legend',
      'li',
      'link',
      'main',
      'map',
      'mark',
      'meta',
      'meter',
      'nav',
      'noframes',
      'noscript',
      'object',
      'ol',
      'optgroup',
      'option',
      'output',
      'p',
      'param',
      'picture',
      'pre',
      'progress',
      'q',
      'rp',
      'rt',
      'ruby',
      's',
      'samp',
      'script',
      'section',
      'select',
      'small',
      'source',
      'span',
      'strike',
      'strong',
      'style',
      'sub',
      'summary',
      'sup',
      'svg',
      'table',
      'tbody',
      'td',
      'template',
      'textarea',
      'tfoot',
      'th',
      'thead',
      'time',
      'title',
      'tr',
      'track',
      'tt',
      'u',
      'ul',
      'var',
      'video',
      'wbr',
   ];

   /**
    * Counts the number of occurrences of a specific HTML element in a given HTML string.
    *
    * @param string $elementName The name of the HTML element to count.
    * @param string $htmlString The HTML string to search within.
    * @return int The number of occurrences of the HTML element.
    */
   public static function countElement(string $elementName, string $htmlString): int
   {
      $dom = new \DOMDocument();
      @$dom->loadHTML($htmlString);
      return @$dom->getElementsByTagName($elementName)->count();
   }

   /**
    * Checks if a given HTML element name is valid.
    *
    * @param string $elementName The HTML element name to check.
    * @return bool True if the element name is valid, false otherwise.
    */
   public static function isValidElement(string $elementName): bool
   {
      return in_array($elementName, self::HTML_ELEMENT_NAMES);
   }
}
