/**
 * @class Denkmal_Form_EventAdd
 * @extends CM_Form_Abstract
 */
var Denkmal_Form_EventAdd = CM_Form_Abstract.extend({

  /** @type String */
  _class: 'Denkmal_Form_EventAdd',

  /** @type Denkmal_Component_Event|Null */
  _preview: null,

  childrenEvents: {
    'Denkmal_FormField_Venue add': function(view, data) {
      var nextField = this.getField('date');
      if (data.new) {
        this.$('.venueDetails').show();
        nextField = this.getField('venueAddress');
      }
      this.setTimeout(function() {
        nextField.setFocus();
      }, 50);
    },
    'Denkmal_FormField_Venue delete': function() {
      this.$('.venueDetails').hide();
    }
  },

  ready: function() {
    _.each(this.getChildren('CM_FormField_Text'), function(field) {
      field.enableTriggerChange();
    });

    this.on('change', function() {
      this.renderPreview();
    });
  },

  renderPreview: function() {
    var form = this;
    this.submit('Preview', {handleErrors: false}).done(function(response) {
        form._injectView(response, function() {
          if (form._preview) {
            form._preview.replaceWith(this);
          } else {
            form.$el.append(this.$el);
          }
          form._preview = this;
        });
    }).fail(function() {
      form._removePreview();
    });
  },

  _removePreview: function() {
    if (this._preview) {
      this._preview.remove();
      this._preview = null;
    }
  }
});
