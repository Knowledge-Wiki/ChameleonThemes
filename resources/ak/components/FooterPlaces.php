<?php
/**
 * This file is part of the MediaWiki extension ChameleonComponents.
 *
 * ChameleonComponents is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ChameleonComponents is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ChameleonComponents.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup extensions
 * @author thomas-topway-it <support@topway.it>
 * @copyright Copyright ©2024, https://wikisphere.org
 */
 
// @credits https://github.com/ProfessionalWiki/chameleon/blob/master/src/FooterPlaces.php


namespace Skins\Chameleon\Components;

use Skins\Chameleon\IdRegistry;

class FooterPlaces extends Component {

	/**
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function getHtml() {
		return $this->indent() . '<!-- places -->' .
			IdRegistry::getRegistry()->element(
				'ul',
				[ 'id' => 'footer-places', 'class' => $this->getClassString() ],
				implode( $this->getFooterLinks() ),
				$this->indent()
			);

	}

	/**
	 * @return array
	 * @throws \MWException
	 */
	private function getFooterLinks() {
		$footerlinks = $this->getSkinTemplate()->getFooterLinks();

		if ( !array_key_exists( 'places', $footerlinks ) ) {
			return [];
		}

		$this->indent( 1 );

		$links = [];
		foreach ( $footerlinks[ 'places' ] as $key ) {
			$links[] = $this->indent() . '<li>' . $this->getSkinTemplate()->get( $key ) . '</li>';
		}

		$this->indent( -1 );
		return $links;
	}
}
