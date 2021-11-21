var config = {
	paths: {            
    	'slick': "Magelearn_AdvanceCart/js/slick"
    },   
    map: {
        '*': {
        	'Magento_Checkout/template/minicart/item/default.html': 'Magelearn_AdvanceCart/template/minicart/item/default.html',
            'sidebar': 'Magelearn_AdvanceCart/js/sidebar',
            'Magento_Checkout/js/view/minicart': 'Magelearn_AdvanceCart/js/view/minicart'
        }
    },
    shim: {
		'slick': {
	    	deps: ['jquery']
	    }
	}
};
