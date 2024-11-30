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
 
// @credits https://github.com/ProfessionalWiki/chameleon/blob/master/src/Components/Menu.php

namespace Skins\Chameleon\Components\NavbarHorizontal;

use Sanitizer;
use Skins\Chameleon\Components\Component;
use Skins\Chameleon\Menu\MenuFactory;

/**
 * Class Menu
 *
 * @author Stephan Gambke
 * @since 1.0
 * @ingroup Skins
 */
class Menu extends Component {

	/**
	 * Builds the HTML code for this component
	 *
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function getHtml() {
		if ( $this->getDomElement() === null ) {
			return '';
		}

		$menu = $this->getMenu();

		$menu->setMenuItemFormatter( function ( $href, $class, $text, $depth, $subitems ) {
			$href = Sanitizer::cleanUrl( $href );
			$text = htmlspecialchars( $text );

			if ( $depth === 1 && !empty( $subitems ) ) {
				return "<li class=\"\"><a class=\"$class\" href=\"#!\">$text</a>$subitems</li>";
			} else {
				return "<li class=\"\"><a class=\"$class\"  href=\"$href\">$text</a>$subitems</li>";
			}

		} );

		$menu->setItemListFormatter( function ( $rawItemsHtml, $depth ) {
			if ( $depth === 0 ) {
				return $rawItemsHtml;
			} elseif ( $depth === 1 ) {
				return "<ul class=\"nav-dropdown\">$rawItemsHtml</ul>";
			} else {
				//return "<div>$rawItemsHtml</div>";
				return "<ul>$rawItemsHtml</ul>";
			}
		} );

		return $menu->getHtml();
	}

	/**
	 * @return \Skins\Chameleon\Menu\Menu
	 * @throws \MWException
	 */
	public function getMenu() {
		$domElement = $this->getDomElement();
		$msgKey = $domElement->getAttribute( 'message' );

		$menuFactory = new MenuFactory();

		if ( empty( $msgKey ) ) {
			return $menuFactory->getMenuFromMessageText( $domElement->textContent );
		} else {
			return $menuFactory->getMenuFromMessage( $msgKey );

		}
	}
}
