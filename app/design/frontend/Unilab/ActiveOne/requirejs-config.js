var config = {
    paths: {
            'fancybox': 'js/jquery.fancybox',
            'unilab': 'js/unilab',
            'ddaccordion': 'js/ddaccordion',
            'effects': 'js/scriptaculous/effects',
            'controls': 'js/scriptaculous/controls',
            'capture_image': 'js/js_camera/capture_image',
            'webcam': 'js/js_camera/webcam',
            'elavateZoom': 'js/jquery_zoom/jquery.elevatezoom'
    } ,
    shim: {
        'fancybox': ['jquery'],
        'effects': ['prototype'],
        'controls': ['effects'],
        'unilab': ['controls'],
        'webcam' : ['prototype'],
        'capture_image': ['jquery'],
        'elavateZoom': ['jquery']
    },
    map: {
        "*": {
            'prescription-validation': "js/prescription-validation",
            'jquery-form': "js/jquery.form.min"
        }
    }
};