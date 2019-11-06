var config = {
    map: {
        '*': {
            'Unilab/note': 'Unilab_Bannerslider/js/jquery/slider/jquery-ads-note',
        },
    },
    paths: {
        'Unilab/flexslider': 'Unilab_Bannerslider/js/jquery/slider/jquery-flexslider-min',
        'Unilab/evolutionslider': 'Unilab_Bannerslider/js/jquery/slider/jquery-slider-min',
        'Unilab/zebra-tooltips': 'Unilab_Bannerslider/js/jquery/ui/zebra-tooltips',
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
