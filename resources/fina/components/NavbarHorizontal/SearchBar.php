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
 
// @credits https://github.com/ProfessionalWiki/chameleon/blob/master/src/Components/SearchBar.php

namespace Skins\Chameleon\Components\NavbarHorizontal;

use Linker;
use Skin;
use Skins\Chameleon\Components\Component;
use Skins\Chameleon\IdRegistry;

class SearchBar extends Component {

	/**
	 * @return string
	 * @throws \MWException
	 */
	public function getHtml() {
		$attribsSearchFormWrapper = \Html::expandAttributes( [
				'id'    => IdRegistry::getRegistry()->getId( 'p-search' ),
				'class' => 'p-search ' . $this->getClassString(),
				'role'  => 'search',
			]
		);

		$tooltipSearchFormWrapper = Linker::tooltip( 'p-search' );

		$attribsSearchForm = \Html::expandAttributes( [
				'id'    => IdRegistry::getRegistry()->getId( 'searchform' ),
				'class' => 'mw-search',
				'action' => $this->getSkinTemplate()->data[ 'wgScript' ],
			]
		);

		$ret = $this->indent() . '<!-- search form -->' .
			$this->indent( 1 ) . "<form $attribsSearchForm >" .
			$this->indent( 1 ) . "<input type=\"hidden\" name=\"title\" value=\" {$this->getSkinTemplate()->data[ 'searchtitle' ]}\" />" .

			$this->indent() . '<div class="input-group">' .
			$this->indent( 1 ) . $this->getSearchInputHtml() .
			$this->indent() . '</div>' .
			$this->indent( -1 ) . '</form>';
	// *** attention !!!
	// put the link after the text input otherwise
	// searchSuggest.js won't render suggestions-special box !!
	return '<li class="">
<div class="kma-search-wrapper">'
. $ret
. '</div><a class="no-symbol page-tools-kda-search" href="#!"></a></li>';
	}

	/**
	 * @return string
	 */
	private function getSearchInputHtml(): string {
		$attributes = [
			'id' => IdRegistry::getRegistry()->getId( 'searchInput' ),
			'type' => 'text',
			'class' => 'form-control'
		];

		if ( $this->getAttribute( 'placeholder' ) !== '' ) {
			$attributes['placeholder'] = $this->getAttribute( 'placeholder' );
		}

		return $this->getSkin()->makeSearchInput( $attributes ) ?? '';
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	private function getGoButton() {
		$valueAttr = 'searcharticle';
		$idAttr = 'searchGoButton';
		$nameAttr = 'go';
		$glyphicon = ( $this->getAttribute( 'buttons' ) === 'go' ? 'search' : 'go' );

		return $this->getButton( 'go', $valueAttr, $idAttr, $nameAttr, $glyphicon );
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	private function getSearchButton() {
		$valueAttr = 'searchbutton';
		$idAttr = 'mw-searchButton';
		$nameAttr = 'fulltext';
		$glyphicon = 'search';

		return $this->getButton( 'search', $valueAttr, $idAttr, $nameAttr, $glyphicon );
	}

	/**
	 * @param string $button
	 * @param string $valueAttr
	 * @param string $idAttr
	 * @param string $nameAttr
	 * @param string $glyphicon
	 *
	 * @return string
	 * @throws \MWException
	 */
	private function getButton( $button, $valueAttr, $idAttr, $nameAttr, $glyphicon ) {
		if ( $this->shouldShowButton( $button ) ) {

			$buttonAttrs = [
				'value' => wfMessage( $valueAttr )->text(),
				'id' => IdRegistry::getRegistry()->getId( $idAttr ),
				'name' => $nameAttr,
				'type' => 'submit',
				'class' => $glyphicon . '-btn ' . $idAttr,
				'aria-label' => $this->getSkinTemplate()->getMsg( 'chameleon-search-aria-label' )->text()
			];

			$buttonAttrs = array_merge(
				$buttonAttrs,
				Linker::tooltipAndAccesskeyAttribs( "search-$nameAttr" )
			);

			return $this->indent() . \Html::rawElement( 'button', $buttonAttrs );
		}

		return '';
	}

	/**
	 * @param string $button
	 *
	 * @return bool
	 */
	private function shouldShowButton( $button ) {
		$buttonsAttribute = $this->getAttribute( 'buttons' );
		return ( $button === 'go' && $buttonsAttribute !== 'search' ) ||
			( $button === 'search' && $buttonsAttribute !== 'go' );
	}
}
