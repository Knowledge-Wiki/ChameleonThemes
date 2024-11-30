<?php
/**
 * This file is part of the MediaWiki extension ChameleonThemes.
 *
 * ChameleonThemes is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ChameleonThemes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ChameleonThemes.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup extensions
 * @author thomas-topway-it <support@topway.it>
 * @copyright Copyright ©2024, https://wikisphere.org
 */
 
// @credits https://github.com/ProfessionalWiki/chameleon/blob/master/src/Footer.php

namespace Skins\Chameleon\Components;

use Skins\Chameleon\IdRegistry;
use Skins\Chameleon\Menu\MenuFactory;
use Sanitizer;

class Footer extends Component {
	/**
	 * @return string
	 * @throws \MWException
	 */
	public function getHtml() {
		$mainPage = \Title::newMainPage();
		$title = $this->getSkin()->getTitle();

		$isMainPage = $mainPage->getText() === $title->getText();

		// ***first get menu from the message (pagename) attribute
		$menu = $this->getMenu();

		$links = [];
		$menu->setMenuItemFormatter(function (
			$href,
			$class,
			$text,
			$depth,
			$subitems
		) use (&$links) {
			$href = Sanitizer::cleanUrl($href);
			$text = htmlspecialchars($text);

			if ($depth === 1) {
				if (!empty($subitems)) {
					return "<div class=\"col-md-3 mt-4\"><h5 class=\"font-weight-bold text-uppercase\">$text</h5>$subitems</div>";
				} else {
					$links[] = "<a class=\"$class\" href=\"$href\">$text</a>";
					return '';
				}
			} else {
				return "<li class=\"\"><a class=\"$class\" href=\"$href\">$text</a>$subitems</li>";
			}
		});

		$menu->setItemListFormatter(function ($rawItemsHtml, $depth) {
			if ($depth === 0) {
				return $rawItemsHtml;
			} elseif ($depth === 1) {
				return "<ul class=\"list-unstyled\">$rawItemsHtml</ul>";
			} else {
				return "<ul class=\"nested\">$rawItemsHtml</ul>";
			}
		});

		$menuHtml = $menu->getHtml();

		// then retrieve the footer "places" links
		if (empty($menuHtml) && empty($links)) {
			$links = $this->getFooterLinks();
		}

		$linkPerColumn = 4;
		$columnsNoTitle = [];
		$columns = array_chunk($links, $linkPerColumn);

		foreach ($columns as $items) {
			$columnsNoTitle[] =
				"<div class=\"col-md-3 mt-4\"><ul class=\"list-unstyled\">" .
				implode(
					array_map(static function ($value) {
						return "<li class=\"list-unstyled\">$value</li>";
					}, $items),
				) .
				'</ul></div>';
		}

		$ret = '';
		$skinTemplate = $this->getSkinTemplate();

		$footerIcons = $this->getIcons();
		$sidebar = $skinTemplate->get('sidebar');

		$columnsTitle = [];
		foreach ($sidebar as $key => $items) {
			array_walk($items, static function (&$value, $key) use (
				$skinTemplate
			) {
				$value = $skinTemplate->makeListItem($key, $value);
			});

			if (count($items)) {
				$columnsTitle[] =
					"<div class=\"col-md-3 mt-4\"><h5 class=\"font-weight-bold text-uppercase\">$key</h5><ul class=\"list-unstyled\">" .
					implode($items) .
					'</ul></div>';
			}
		}

		// @see https://mdbootstrap.com/docs/b4/jquery/navigation/footer/
		/*
		$ret .= '<footer class="page-footer">';
			$ret .= $this->indent( 1 );

			$ret .= '<div class="container ">';
				$ret .= $this->indent( 1 );
				$ret .= '<!-- Grid row -->';
				$ret .= '<div class="row footer-row1 justify-content-center align-items-top">';


					$ret .= implode( $columnsTitle );


				$ret .= '</div>';




			$ret .= '<div class="row pt-4 text-center footer-row2 justify-content-center align-items-top">';

					$ret .= $this->indent( 1 );
					$ret .= '<div class="col-md-3" mt-4>';
						$ret .= $this->indent( 1 );
						$ret .= '<img height="150"src="https://fina.knowledge.wiki/footer-logo.png" />';
					$ret .= '</div>';

				$ret .= implode( $columnsNoTitle );
				//$ret .= $menuHtml;

				$ret .= '</div>';


				$ret .= '<div class="row footer-row3 justify-content-right align-items-top">';

						$ret .= '<div class="footer-info">';
					$ret .= implode( $footerIcons );
				$ret .= '</div>';

				$ret .= '</div>';


			$ret .= '</div>';

		$ret .= '</footer>';
*/

		$toolbox = $skinTemplate->get('sidebar')['TOOLBOX'] ?? [];

		$arr = [];
		foreach ($toolbox as $key => $linkItem) {
			//$link = $skinTemplate->makeLink( $key, $linkItem);
			$text =
				$linkItem['text'] ??
				$skinTemplate
					->getSkin()
					->msg($linkItem['msg'] ?? $key)
					->text();
			// var_dump($link);
			$arr[$text] = $linkItem['href'];
		}

		$this->getSkin()
			->getOutput()
			->enableOOUI();

		$options = \Xml::listDropDownOptionsOoui($arr);

		$ret .= '<footer class="page-footer py-4">';

		$ret .= '<div class="container ">';
		$ret .= '<!-- Grid row -->';
		$ret .= '<div class="row footer-row1 pt-4 ">';

		$ret .= '<div class="col-md-12 " >';
		$ret .=
			(!$isMainPage
				? $this->getSkinTemplate()->get('lastmod') . " "
				: '') . $this->getSkinTemplate()->get('copyright');

		$ret .= '</div>';
		$ret .= '</div>';
		$ret .= '<div class="row footer-row2 py-2 pb-4 align-items-center">';

		$ret .= $this->indent(1);
		$ret .= '<div class="col-md-3 py-2 text-md-left text-sm-center ">';

		$ret .= '<ul>';
		$ret .= implode ( array_map( static function($value) {
			return '<li>' . $value . '</li>';
		}, $links ) );
		$ret .= '</ul>';

		$ret .= '</div>';

		$ret .= '<div class="col-md-3 py-2 text-center">';
		$ret .= new \OOUI\ButtonWidget([
			"data" => $options,
			"icon" => 'menu',
			//"classes" => ['mt-sm-3'],
			'id' => "toolbox-ooui-select",
			'label' => $skinTemplate->getMsg('toolbox')->escaped(),
			'infusable' => true,
			//'flags' => [ 'progressive', 'primary' ]
		]);

		$ret .= '</div>';

		$ret .= '<div class="col-md-6 py-2 text-center">';

		$ret .= implode($footerIcons);

		$ret .= '</div>';
		$ret .= '</div>';
		$ret .= '</div>';
		$ret .= '</footer>';

		return $ret;
	}

	/**
	 * @return \Skins\Chameleon\Menu\Menu
	 * @throws \MWException
	 */
	public function getMenu() {
		$domElement = $this->getDomElement();
		$msgKey = $domElement->getAttribute('message');

		$menuFactory = new MenuFactory();

		if (empty($msgKey)) {
			return $menuFactory->getMenuFromMessageText(
				$domElement->textContent,
			);
		} else {
			return $menuFactory->getMenuFromMessage($msgKey);
		}
	}

	/**
	 * @return array
	 * @throws \MWException
	 */
	private function getFooterLinks() {
		$footerlinks = $this->getSkinTemplate()->getFooterLinks();

		if (!array_key_exists('places', $footerlinks)) {
			return [];
		}
		$links = [];
		foreach ($footerlinks['places'] as $key) {
			$links[] = $this->getSkinTemplate()->get($key);
		}

		// $this->indent( -1 );
		return $links;
	}

	////////////////// Toolbox //////////////////////////

	/**
	 * @param int $indent
	 *
	 * @return string[]
	 * @throws \FatalError
	 * @throws \MWException
	 */
	private function getLinkListItems($indent = 0) {
		$this->indent($indent);

		$skinTemplate = $this->getSkinTemplate();

		$listItems = [];

		$toolbox = $skinTemplate->get('sidebar')['TOOLBOX'] ?? [];

		// FIXME: Do we need to care of dropdown menus here? E.g. RSS feeds?
		foreach ($toolbox as $key => $linkItem) {
			if (isset($linkItem['class'])) {
				$linkItem['class'] .= ' nav-item';
			} else {
				$linkItem['class'] = 'nav-item';
			}
			// Add missing id for legacy links
			if (!isset($linkItem['id'])) {
				$linkItem['id'] = 't-' . $key;
			}
			$listItems[] =
				$this->indent() .
				$skinTemplate->makeListItem($key, $linkItem, [
					'link-class' => 'nav-link ' . $linkItem['id'],

					// 'tag' => 'div' ] );
					'tag' => 'li',
				]);
		}

		$this->indent(-$indent);

		return $listItems;
	}

	/**
	 * @param string $labelMsg
	 * @param string $contents
	 *
	 * @return string
	 * @throws \MWException
	 */
	private function wrapDropdownMenu($labelMsg, $contents) {
		$trigger =
			$this->indent(1) .
			IdRegistry::getRegistry()->element(
				'a',
				[
					'href' => '#',
					'class' => 'nav-link dropdown-toggle p-tb-toggle',
					'data-toggle' => 'dropdown',
					'data-boundary' => 'viewport',
				],
				$this->getSkinTemplate()
					->getMsg($labelMsg)
					->escaped(),
			);

		$liElement = IdRegistry::getRegistry()->element(
			'div',
			['class' => 'dropdown-menu'],
			$contents,
			$this->indent(),
		);
		$ulElement = IdRegistry::getRegistry()->element(
			'div',
			['class' => 'nav-item p-tb-dropdown ' . $this->getClassString()],
			$trigger . $liElement,
			$this->indent(-1),
		);

		return $ulElement;
	}

	//////////////////	FooterIcons /////////////////////

	/**
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function FooterIconsGetHtml() {
		return $this->indent() .
			'<!-- footer icons -->' .
			IdRegistry::getRegistry()->element(
				'div',
				['id' => 'footer-icons', 'class' => $this->getClassString()],
				implode($this->getIcons()),
				$this->indent(),
			);
	}

	/**
	 * @return string[]
	 * @throws \MWException
	 */
	private function getIcons() {
		$this->indent(1);

		$lines = [];
		$blocks = $this->getSkinTemplate()->getFooterIconsWithImage() ?? [];

		foreach ($blocks as $blockName => $footerIcons) {
			$lines[] =
				$this->indent() .
				'<!-- ' .
				htmlspecialchars($blockName) .
				' -->';

			foreach ($footerIcons as $icon) {
				$lines[] =
					$this->getSkinTemplate()
						->getSkin()
						->makeFooterIcon($icon);
			}
		}

		$this->indent(-1);
		return $lines;
	}
}

