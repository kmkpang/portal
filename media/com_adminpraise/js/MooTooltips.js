var tippable = new Class({
    Implements: [Options, Events],
    
    options: {
        text: "this be a tip",
        duration: 100,
        topOffset: 40,
        topOffsetStart: 40
    },
    
    initialize: function(element, options) {
        this.setOptions(options);
        this.element = document.id(element);
        if (!this.element)
            return;
                
        this.attachTip();
    },
    
    attachTip: function() {
        this.createTip();
        this.attachEvents();
    },
    
    createTip: function() {
        this.event = "show";
        this.tip = new Element("div", {
            'class': 'hybridDesc',
			
			styles: {
                opacity: 0,
                marginLeft: -this.element.getSize().x / 2 // center
            }
        }).set("morph", {
            duration: this.options.duration,
            link: "cancel",
            onComplete: function() {
                this.fireEvent(this.event);
            }.bind(this)
        });
      
        this.body = new Element("div", {
			'class': 'body',
            html: this.options.text
        }).inject(this.tip);
        
        
        this.tip.inject(this.element, "top");
        
        // now center it.
        var tipWidth = this.tip.getSize().x, elWidth = this.element.getSize().x;

        this.tip.setStyle("marginLeft", (elWidth - tipWidth) / 2);
        
    },
    
    attachEvents: function() {
        this.element.addEvents({
            mouseenter: this.showTip.bind(this),
            mouseleave: this.hideTip.bind(this)
        });
    },
    
    showTip: function() {
        this.fireEvent("beforeShow");
        this.event = "show";
        this.tip.morph({
            marginTop: [-this.options.topOffsetStart, -this.options.topOffset],
            opacity: [0, 1]
        });
        
    },
    
    hideTip: function() {
        this.event = "hide";
        this.tip.morph({
            marginTop: -this.options.topOffsetStart,
            opacity: [1, 0]
        });    
        this.fireEvent("beforeHide");
    }
    
});