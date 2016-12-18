/**
 * @class Denkmal_Component_MessageList_Abstract
 * @extends Denkmal_Component_Abstract
 */
var Denkmal_Component_MessageList_Abstract = Denkmal_Component_Abstract.extend({

  /** @type String */
  _class: 'Denkmal_Component_MessageList_Abstract',

  /** @type {Boolean} */
  canDelete: null,

  events: {
    'click .deleteMessage': function(event) {
      var messageId = $(event.currentTarget).closest('.message').data('id');
      this.deleteMessage(messageId);
    },
    'click .showImage': function(event) {
      var $image = $(event.currentTarget);
      this.showImage($image);
    }
  },

  /**
   * @param {jQuery} $image
   */
  showImage: function($image) {
    $image.floatbox();

    $image.on('click.closeImage', function() {
      $image.floatbox('close');
    });
    $image.on('floatbox-close', function() {
      $image.off('click.closeImage');
    });
  },

  /**
   * @param {Number} messageId
   */
  deleteMessage: function(messageId) {
    this.ajaxModal('deleteMessage', {message: messageId});
  },

  /**
   * @param {Object} message
   */
  prependMessage: function(message) {
    if (this.$('.messageList > .message[data-id="' + message.id + '"]').length > 0) {
      return;
    }
    this.renderTemplate('template-message', {
      id: message.id,
      created: message.created,
      venue: message.venue,
      hasText: message.text !== null,
      text: message.text,
      hasImage: message.image !== null,
      imageUrl: (message.image !== null) ? message.image['url-thumb'] : null,
      hasTags: (message.tagList.length > 0),
      tagList: message.tagList,
      hasUser: (message.user !== null),
      user: message.user,
      canDelete: this.canDelete
    }).prependTo(this.$('.messageList'));
  }
});
