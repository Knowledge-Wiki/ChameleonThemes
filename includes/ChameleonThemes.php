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

use MediaWiki\MediaWikiServices;

class ChameleonThemes {

	/**
	 * @param User|null $user
	 */
	public static function initialize( $user ) {
	}

	/**
	 * @param Parser $parser
	 * @param mixed ...$argv
	 */
	public static function parserFunctioNoparse( Parser $parser, ...$argv ) {
		return [
			'text' => $argv[0],
			'noparse' => true,
			'isHTML' => true
		];
	}

	/**
	 * @param User|null $user
	 */
	public static function initTheme() {
		// $bootstrapManager = \Bootstrap\BootstrapManager::getInstance();
		$theme = $GLOBALS['wgChameleonThemesTheme'];
		$localPath = $GLOBALS['wgExtensionDirectory'] . "/ChameleonThemes/resources/$theme";
		if ( empty( $theme ) ||  !file_exists( $localPath ) ) {
			// throw new MWException( "theme $theme not found" );
			return;
		}

		// include chameleon components
		self::autoloadRec( "$localPath/components" );

		$resources = include( "$localPath/resources.php");

		// chameleon layout
		if ( !empty( $resources['layout'] ) ) {
			$GLOBALS['egChameleonLayoutFile'] = "$localPath/{$resources['layout']}";
		}

		if ( !is_array( $GLOBALS[ 'egChameleonExternalStyleModules'] ) ) {
			$GLOBALS[ 'egChameleonExternalStyleModules'] = [];
		}

		if ( is_array( $resources['scss'] ) ) {
			foreach ( $resources['scss'] as $file ) {
				$GLOBALS[ 'egChameleonExternalStyleModules'][] = "$localPath/$file";
			}
		}

		if ( is_array( $resources['module'] ) ) {
			$remotePath = $GLOBALS[ 'wgExtensionAssetsPath'] . "/ChameleonThemes/resources/$theme";
			$module = [
				'localBasePath' => $localPath,
				'remoteExtPath' => $remotePath,
			];
			$GLOBALS['wgResourceModules']["ext.ChameleonThemes.$theme"] = array_merge( $module, $resources['module'] );
		}
	}

	/**
	 * @param string $dir
	 */
	private static function autoloadRec( $dir ) {
    	$files = scandir( $dir );

		foreach ($files as $key => $value) {
			$path = realpath( $dir . DIRECTORY_SEPARATOR . $value );
			if ( !is_dir( $path ) ) {
				include_once( $path );
			} else if ($value != '.' && $value != '..' ) {
				self::autoloadRec( $path );
			}
		}
	}

}
