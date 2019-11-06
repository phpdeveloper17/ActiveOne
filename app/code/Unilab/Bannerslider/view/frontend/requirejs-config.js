var config = {
	map: {
		'*': {
			'Unilab/note': 'Unilab_Bannerslider/js/jquery/slider/jquery-ads-note',
			'Unilab/impress': 'Unilab_Bannerslider/js/report/impress',
			'Unilab/clickbanner': 'Unilab_Bannerslider/js/report/clickbanner',
		},
	},
	paths: {
		'Unilab/flexslider': 'Unilab_Bannerslider/js/jquery/slider/jquery-flexslider-min',
		'Unilab/evolutionslider': 'Unilab_Bannerslider/js/jquery/slider/jquery-slider-min',
		'Unilab/popup': 'Unilab_Bannerslider/js/jquery.bpopup.min',
	},
	shim: {
		'Unilab/flexslider': {
			deps: ['jquery']
		},
		'Unilab/evolutionslider': {
			deps: ['jquery']
		},
		'Unilab/zebra-tooltips': {
			deps: ['jquery']
		},
	}
};
