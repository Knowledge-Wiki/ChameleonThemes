/**
 * @licence GPL-2.0-or-later
 * @author thomas-topway-it for KM-A
 */

$(document).ready(function () {

	/////////////////////// dropdown / search input ///////////////////

	$('nav ul li > a:not(:only-child)').click(function (e) {

		$(this).siblings('.nav-dropdown').fadeToggle(150);

		$('.nav-dropdown').not($(this).siblings()).hide();

		if (!$(this).hasClass('page-tools-kda-search')) {
			hideSearchInput();
		}
		e.stopPropagation();
	});
	
	$('html').click(function () {
		$('.nav-dropdown').fadeOut(150);
	});

	$(window).click(function () {
		hideSearchInput();
	});

	$('.nav-container .kma-search-wrapper').click(function (event) {
		event.stopPropagation();
	});
	
	$('nav a.page-tools-kda-search').click(function (e) {
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


	/////////////////////// nav toggle ///////////////////

	document.querySelector('.nav-toggle a').addEventListener('click', function () {
		this.classList.toggle('active');
		
		$('.navigation .nav-mobile').toggle();
	});


	/////////////////////// edit icon ///////////////////

	$('.nav-container .personal-tools-kda-edit').click(function () {
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
	
	
	/////////////////////// footer ///////////////////

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

});

