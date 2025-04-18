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
 *
 * @license GPL-2.0-or-later
 * @author thomas-topway-it for KM-A
 */
 
// @credits https://github.com/ProfessionalWiki/chameleon/blob/master/src/FooterInfo.php


namespace Skins\Chameleon\Components;

use Skins\Chameleon\IdRegistry;

class FooterInfoKma extends Component {

	/**
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function getHtml() {
		return '';
	}

	/**
	 * @return string[]
	 * @throws \MWException
	 */
	private function getFooterLines() {
		$footerlinks = $this->getSkinTemplate()->getFooterLinks();

		$this->indent( 1 );

		$lines = [];
		foreach ( $footerlinks as $category => $msgKeys ) {

			if ( $category !== 'places' ) {

				$lines[] = $this->indent() . '<!-- ' . htmlspecialchars( $category ) . ' -->';
				foreach ( $msgKeys as $key ) {
					$lines[] = $this->indent() . '<div>' . $this->getSkinTemplate()->get( $key ) . '</div>';
				}
			}
		}

		$this->indent( -1 );
		return $lines;
	}
}
