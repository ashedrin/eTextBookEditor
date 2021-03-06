var eTextBookRule = Backbone.Model.extend({
    initialize: function() {
        this.cont = this.get('cont');
        this.editElements = {};
        this.editable = false;
        this.addControlPanel();
        this.addEditElements();
    }

    ,addControlPanel: function() {

        var $this = this;

        this.get('cont').prepend(App.eTextBookTemplate.getTemplate('blockControlPanel'));

        this.get('cont').append(this.get('module').generateAddBlockButton());

        this.get('cont').find('control-panel .edit').bind('click', function() {
            if(!$this.editable) {
                $this.startEdit();
            } else { $this.finishEdit(); }
        });

        this.get('cont').find('control-panel .remove').bind('click', function() {
            $this.get('cont').remove();
            $this = null;
            App.eTextBookEditor.updateDisplay();
        });
    }

    ,startEdit: function() {
        this.get('cont').attr('editable', 1);
        this.editable = true;
        this.cont.find('rule-title textarea').code(this.cont.find('rule-title view-element').html());
    }

    ,finishEdit: function() {
        this.get('cont').attr('editable', 0);
        this.editable = false;
        this.cont.find('rule-title view-element').html(this.cont.find('rule-title textarea').code());
        App.eTextBookEditor.updateDisplay();
    }

    ,addEditElements: function() {
        this.appendEditElement('title', new inlineEditTextarea({
            cont: this.cont.find('rule-title')
        }));
        this.cont.find('rule-title textarea').summernote(App.eTextBookEditor.toolbarConfig);
    }

    ,appendEditElement: function(title, element) {
        this.editElements[title] = element;
    }
});
