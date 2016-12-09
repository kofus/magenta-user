<?php

namespace Kofus\System\View\Helper\Navigation;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View\Exception;

/**
 * Helper for rendering menus from navigation containers
 */
class DropdownMenu extends \Zend\View\Helper\Navigation\Menu
{
	
	/**
	 * Returns an HTML string containing an 'a' element for the given page if
	 * the page's href is not empty, and a 'span' element if it is empty
	 *
	 * Overrides {@link AbstractHelper::htmlify()}.
	 *
	 * @param  AbstractPage $page               page to generate HTML for
	 * @param  bool         $escapeLabel        Whether or not to escape the label
	 * @param  bool         $addClassToListItem Whether or not to add the page class to the list item
	 * @return string
	 */
	public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
	{
		// get attribs for element
		$attribs = array(
				'id'     => $page->getId(),
				'title'  => $this->translate($page->getTitle(), $page->getTextDomain()),
		);
	
		if ($addClassToListItem === false) {
			$attribs['class'] = $page->getClass();
		}
		
		if ($page->get('attribs')) {
			foreach ($page->get('attribs') as $key => $value)
				$attribs[$key] = $value;
		}
	
		// does page have a href?
		$href = $page->getHref();
		if ($href) {
			$element = 'a';
			$attribs['href'] = $href;
			$attribs['target'] = $page->getTarget();
		} else {
			$element = 'span';
		}
	
		$html  = '<' . $element . $this->htmlAttribs($attribs) . '>';
		
		$label = '';
		if (! $page->get('hideLabel'))
			$label = $this->translate($page->getLabel(), $page->getTextDomain());
		
		if ($escapeLabel === true && ! $page->get('escape') == 'false') {
			/** @var \Zend\View\Helper\EscapeHtml $escaper */
			$escaper = $this->view->plugin('escapeHtml');
			$label = $escaper($label);
		}
		if ($page->get('icon')) {
			$escaperAttr = $this->view->plugin('escapeHtml');
			$label = '<i class="'.$escaperAttr($page->get('icon')).'"></i> ' . $label;
		}
		
		$html .= $label;
		
		
		if ($page->hasPages() && $this->depth < $this->getMaxDepth())
			$html .= ' <span class="caret"></span>';
		
		$html .= '</' . $element . '>';
	
		return $html;
	}
	


    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  AbstractContainer $container          container to render
     * @param  string            $ulClass            CSS class for first UL
     * @param  string            $indent             initial indentation
     * @param  int|null          $minDepth           minimum depth
     * @param  int|null          $maxDepth           maximum depth
     * @param  bool              $onlyActive         render only active branch?
     * @param  bool              $escapeLabels       Whether or not to escape the labels
     * @param  bool              $addClassToListItem Whether or not page class applied to <li> element
     * @param  string            $liActiveClass      CSS class for active LI
     * @return string
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem,
        $liActiveClass
    ) {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);
        /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');

        if ($found) {
            $foundPage  = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST
        );
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $this->depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($this->depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } elseif ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages(!$this->renderInvisible) ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $this->depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $this->depth);
	
            // begin new level
            if ($this->depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $this->depth ==  0) {
                    $ulClass = ' class="' . $escaper($ulClass) . '"';
                    
                } elseif ($this->depth > 0) {
                	$ulClass = ' class="dropdown-menu" role="menu"';
                
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;
                
            // end current level
            } elseif ($prevDepth > $this->depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $this->depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
                
            // continue current level
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            }
            
            if ($page->hasPages() && $this->depth < $this->getMaxDepth()) {
	            $page->setClass('dropdown-toggle');
	            $page->set('attribs', array(
	            		'data-toggle' => 'dropdown',
	            		'data-hover' => 'dropdown'
	            ));
            }

            // render li tag and page
            $liClasses = array();
            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;
            }
            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ', $liClasses)) . '"';

            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL
                . $myIndent . '        ' . $this->htmlify($page, $escapeLabels, $addClassToListItem) . PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $this->depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . PHP_EOL
                    . $myIndent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

}
