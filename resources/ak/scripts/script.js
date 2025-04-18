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
 * @author thomas-topway-it for KM-A
 */

$(document).ready(function () {
	var data = $('#toolbox-ooui-select').data();
	var items = [];

	for (var val of data.ooui.data) {
		items.push(new OO.ui.MenuOptionWidget(val));
	}

	var buttonMenu = new OO.ui.ButtonMenuSelectWidget({
		label: data.ooui.label,
		icon: 'menu',
		//	flags: ["progressive", "primary"],
		//	"classes" : ['mt-sm-3'],
		menu: {
			items: items,
		},
		id: 'toolbox-ooui-select',
	});

	buttonMenu.getMenu().on('choose', function (menuOption) {
		window.location = menuOption.getData();
	});

	$('#toolbox-ooui-select').replaceWith(buttonMenu.$element);

	$('.nav-list.right li').each(function (index, element) {
		$(this)
			.find('a')
			.next()
			.clone()
			.appendTo($('.nav-list-mobile li').get(index));
	});


	$('.nav-container .personal-tools-kma-edit').click(function () {
		var edit = mw.cookie.get('kmaskin-show-nav-edit');

		if (!edit) {
			mw.cookie.set('kmaskin-show-nav-edit', true, {
				path: '/',
				// not set: session cookie
				// expires: 365 * 86400
			});
		} else {
			mw.cookie.set('kmaskin-show-nav-edit', null);
		}

		$('#p-contentnavigation')
			.fadeToggle(150)
			.css('display', edit ? 'flex' : 'none');
	});

	$(function () {
		$('nav ul li > a:not(:only-child)').click(function (e) {
			$(this).siblings('.nav-dropdown').fadeToggle(150);
			$('.nav-dropdown').not($(this).siblings()).hide();
			if (!$(this).hasClass('page-tools-kma-search')) {
				hideSearchInput();
			}
			e.stopPropagation();
		});
		$('html').click(function () {
			$('.nav-dropdown').fadeOut(150);
		});
	});
	document.querySelector('#nav-toggle').addEventListener('click', function () {
		this.classList.toggle('active');
	});
	$('#nav-toggle').click(function () {
		$('nav ul.nav-list:not(.right)').toggle();
	});

	$(window).click(function () {
		hideSearchInput();
	});

	$('.nav-container .kma-search-wrapper').click(function (event) {
		event.stopPropagation();
	});

	$('nav a.page-tools-kma-search').click(function (e) {
		if ($('.nav-container .kma-search-wrapper').first().css('opacity') == 0) {
			showSearchInput();
		} else {
			hideSearchInput();
		}

		$(this).siblings('.kma-search-wrapper input').focus();
		e.stopPropagation();
	});

	function showSearchInput() {
		$('.nav-container .kma-search-wrapper').css({
			'pointer-events': 'auto',
			opacity: '1',
			visibility: 'visible',
			transform: 'translate3d(0,0,0)',
		});
	}

	function hideSearchInput() {
		$('.nav-container .kma-search-wrapper').css({
			'pointer-events': 'none',
			opacity: '0',
			visibility: 'hidden',
			transform: 'translate3d(0,20px,0)',
		});
	}
});

