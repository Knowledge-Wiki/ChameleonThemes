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
 
// @credits https://github.com/ProfessionalWiki/chameleon/blob/master/src/NavbarHorizontal.php


namespace Skins\Chameleon\Components;

use DOMElement;
use Skins\Chameleon\IdRegistry;
use Linker;

class NavbarHorizontal extends Component {
	private $mHtml = null;
	private $htmlId = null;

	/**
	 * @return String
	 * @throws \MWException
	 */
	public function getHtml() {
		$this->getSkin()->getOutput()->addStyle('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap');
		$navElements = $this->buildNavBarComponents();

/*

<ul class="nav-list-mobile">
<li class="icon"><a class="no-symbol page-tools-kda-search" href="#!"></a>'
 . ( $this->getSkin()->getUser()->isRegistered() ? "<li><a class=\"personal-tools-kda-edit\" href=\"#!\"></a></li>" : '' )
. '<li class="icon"><a class="no-symbol personal-tools-kda-user" href="#!"></a></li>
</ul>
*/
		return '<section class="navigation">
  <div class="nav-container">
    <div class="nav-brand">
      ' . $this->getLogo() . '
    </div>
    <nav class="nav-desktop">
      <ul class="nav-list">
' . implode( '', $navElements['left'] ) . '
      </ul>
    </nav>
    <nav class="nav-right">
      <ul class="nav-list">
' . implode( '', $navElements['right'] ) . '
      </ul>
      <div class="nav-toggle">
        <a href="#!"><span></span></a>
      </div>
    </nav>
  </div>
    <nav class="nav-mobile">    
      <ul class="nav-list">
' . implode( '', $navElements['left'] ) . '
      </ul>
    </nav>
</section>';
	}

	/**
	 * @return array
	 */
	public function getResourceLoaderModules() {
		return [];
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	protected function buildNavBarComponents() {
		return $this->buildNavBarElementsFromDomTree();
	}

	/**
	 * @return string[][]
	 * @throws \MWException
	 */
	protected function buildNavBarElementsFromDomTree() {
		$elements = [
			'head'  => [],
			'left'  => [],
			'right' => [],
		];

		/** @var DOMElement[] $children */
		$children = $this->getDomElement()->hasChildNodes() ? $this->getDomElement()->childNodes : [];

		// add components
		foreach ( $children as $node ) {
			$this->buildAndCollectNavBarElementFromDomElement( $node, $elements );
		}
		return $elements;
	}

	/**
	 * @param DOMElement $node
	 * @param array &$elements
	 *
	 * @throws \MWException
	 */
	protected function buildAndCollectNavBarElementFromDomElement( $node, &$elements ) {
		if ( $node instanceof DOMElement && $node->tagName === 'component' &&
			$node->hasAttribute( 'type' ) ) {

			$position = $node->getAttribute( 'position' );

			if ( !array_key_exists( $position, $elements ) ) {
				$position = 'left';
			}

			$indentation = 0;

			if ( $position !== 'head' && $this->isCollapsible() ) {
				$indentation++;
			}

			if ( $position === 'right' ) {
				$indentation++;
			}

			$this->indent( $indentation );
			$html = $this->buildNavBarElementFromDomElement( $node );
			$this->indent( -$indentation );

			$elements[ $position ][] = $html;

		// } else {
			// TODO: Warning? Error?
		}
	}

	/**
	 * @param \DomElement $node
	 *
	 * @return string
	 * @throws \MWException
	 */
	protected function buildNavBarElementFromDomElement( $node ) {
		return $this->getSkin()->getComponentFactory()->getComponent( $node,
			$this->getIndent() )->getHtml();
	}

	/**
	 * @param string[] $headElements
	 *
	 * @return string
	 * @throws \MWException
	 */
	protected function buildHead( $headElements ) {
		return implode( '', $headElements );
	}

	/**
	 * @param string[] $leftElements
	 * @param string[] $rightElements
	 * @param int $indent
	 *
	 * @return string
	 * @throws \MWException
	 */
	protected function buildTail( $leftElements = [], $rightElements = [], $indent = 0 ) {
		$this->indent( $indent );

		$tail = '';
		if ( $leftElements ) {
			$tail .= implode( '', $leftElements );

		}

		if ( $rightElements ) {
			$tail .= IdRegistry::getRegistry()->element( 'div', [ 'class' => 'navbar-nav right' ],
				implode( '', $rightElements ), $this->indent() );
		}

		$this->indent( -$indent );
		return $tail;
	}

	/**
	 * @param string $tail
	 *
	 * @return string
	 * @throws \MWException
	 */
	private function wrapDropdownMenu( $tail ) {
		$id = IdRegistry::getRegistry()->getId();

		return $this->indent() .
			'<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#' . $id .
			'">' . $this->getTogglerText() . '</button>' .
			IdRegistry::getRegistry()->element( 'div', [ 'class' => 'collapse navbar-collapse',
			'id' => $id ], $tail, $this->indent() );
	}

	/**
	 * @return mixed
	 */
	protected function isCollapsible() {
		return filter_var( $this->getAttribute( 'collapsible', 'true' ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * @return string
	 */
	protected function getTogglerText() {
		if ( !filter_var( $this->getAttribute( 'showTogglerText', 'false' ), FILTER_VALIDATE_BOOLEAN ) ) {
			return '';
		}
		return \Html::rawElement( 'span', [ 'class' => 'navbar-toggler-text' ],
			$this->getSkinTemplate()->getMsg( 'chameleon-toggler' )->escaped() );
	}


	////////////// logo component

	/**
	 * @return string
	 */
	protected function getLogo() {
		$logo = IdRegistry::getRegistry()->element( 'img',
			[
				'src' => $this->getSkinTemplate()->get( 'logopath', '' ),
				'alt' => $this->getSkinTemplate()->get( 'sitename', '' ),
			]
		);

		return $this->getLinkedLogo( $logo );
	}

	/**
	 * @param string $logo
	 * @return string
	 */
	protected function getLinkedLogo( $logo ) {
		if ( $this->shallLink() ) {

			$linkAttributes = array_merge(
				[ 'href' => $this->getLogoLink() ],
				Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
			);

			return IdRegistry::getRegistry()->element( 'a', $linkAttributes, $logo );
		}

		return $logo;
	}

	/**
	 * @return string
	 */
	private function getLogoLink(): string {
		$navUrls = $this->getSkinTemplate()->get( 'nav_urls', [ 'mainpage' => [ 'href' => '#' ] ] );
		$mainPage = $navUrls['mainpage'] ?? [ 'href' => '#' ];
		return $mainPage['href'];
	}

	/**
	 * @return bool
	 */
	private function shallLink() {
		$domElement = $this->getDomElement();

		if ( $domElement === null ) {
			return true;
		}

		$attribute = $domElement->getAttribute( 'addLink' );

		return $attribute === '' || filter_var( $attribute, FILTER_VALIDATE_BOOLEAN );
	}
}
