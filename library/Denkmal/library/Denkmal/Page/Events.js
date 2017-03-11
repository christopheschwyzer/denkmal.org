/**
 * @class Denkmal_Page_Events
 * @extends Denkmal_Page_Abstract
 */
var Denkmal_Page_Events = Denkmal_Page_Abstract.extend({

  /** @type String */
  _class: 'Denkmal_Page_Events',

  /** @type SwipeCarousel */
  _carousel: null,

  _stateParams: ['date', 'event'],

  events: {
    'swipeCarousel-change .swipeCarousel': function(event, data) {
      var delaySetUrl = !data.immediateSetUrl;
      this._onShowPane($(data.element), delaySetUrl);
    },

    'click .dismissBanner': function() {
      this._hideBanner();
    }
  },

  ready: function() {
    this._onShowPaneSetUrlDelayed = _.debounce(this._onShowPaneSetUrl, 2000);

    var $carousel = this.$('.swipeCarousel');
    this._carousel = new SwipeCarousel($carousel);
    this._carousel.init();

    var self = this;
    this.on('destruct', function() {
      self._carousel.destroy();
    });

    this._showBanner();
  },

  /**
   * @param {String} date
   */
  showPane: function(date) {
    var $element = this.$('.dateList > .dateList-item[data-date="' + date + '"]');
    if (!$element.length) {
      throw new Error('Cannot find date list pane for date `' + date + '`');
    }
    if ($element.hasClass('active')) {
      return;
    }
    this._carousel.showPane($element.index(), {immediateSetUrl: true}, !Modernizr.touchevents);
    this._onShowPane($element);
  },

  /**
   * @param {String } date
   * @returns {boolean}
   * @private
   */
  _hasPane: function(date) {
    var $element = this.$('.dateList > .dateList-item[data-date="' + date + '"]');
    return $element.length > 0;
  },

  /**
   * @param {jQuery} $element
   * @param {Boolean} [delaySetUrl]
   */
  _onShowPane: function($element, delaySetUrl) {
    var title = $element.data('title');
    var url = $element.data('url');
    var date = $element.data('date');
    var menuEntryHash = $element.data('menu-hash');

    cm.getDocument()._updateTitle(title);
    cm.getDocument()._activateMenuEntries([menuEntryHash]);

    if (delaySetUrl) {
      this._onShowPaneSetUrlDelayed(url, date);
    } else {
      this._onShowPaneSetUrl(url, date);
    }

    this.trigger('swipe', $element);
  },

  /**
   * @param {String} url
   * @param {String} date
   */
  _onShowPaneSetUrl: function(url, date) {
    if (!$.contains(document, this.el)) {
      return; // View has been destroyed in the meantime
    }
    var nextState = {date: date};
    if (!_.isEqual(nextState, this.getState())) {
      cm.router.pushState(url);
      this.setState(nextState);
    }
  },

  /**
   * @param {String} eventId
   * @returns {Denkmal_Component_Event}
   * @private
   */
  _getEventComponent: function(eventId) {
    eventId = '' + eventId;
    var eventComponentList = {};
    _.each(this.getChildren('Denkmal_Component_EventList'), function(eventListCmp) {
      _.each(eventListCmp.getChildren('Denkmal_Component_Event'), function(eventCmp) {
        eventComponentList['' + eventCmp.getEvent().id] = eventCmp;
      });
    });
    var eventComponent = eventComponentList[eventId];
    if (!eventComponent) {
      throw new Error('Cannot find event component for id `' + eventId + '`');
    }
    return eventComponent;
  },

  /**
   * @param {String} eventId
   * @param {String} date
   */
  showEventDetails: function(eventId, date) {
    var eventComponent = this._getEventComponent(eventId);
    eventComponent.popOut({'fullscreen': true});
  },

  hideEventDetails: function() {
    $('.floatbox').floatbox('close')
  },

  /**
   * @param {Boolean} state
   */
  _changeState: function(state) {
    var date = state['date'];
    if (!date) {
      date = this.$('.dateList > .dateList-item:first').data('date');
      this.setState({date: date});
    }
    if (!this._hasPane(date)) {
      return false;
    }

    this.showPane(date);

    var event = state['event'];
    if (event) {
      this.showEventDetails(event, date);
    } else {
      this.hideEventDetails();
    }
  },

  /**
   * @private
   */
  _showBanner: function() {
    if (null === this.storageGet('bannerVisible')) {
      this.$('.banner').show();
    }
  },

  /**
   * @private
   */
  _hideBanner: function() {
    this.storageSet('bannerVisible', false);
    this.$('.banner').slideUp();
  }
});
