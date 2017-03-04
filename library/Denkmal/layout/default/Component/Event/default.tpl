{block name='event-before'}{/block}
<div class="event">
  <div class="venue-bookmark {if $isPersistent}toggleVenueBookmark{/if}" {if $isPersistent}data-venue-id="{$venue->getId()}"{/if} {if $isBookmarked}data-bookmarked{/if}>
    {resourceFileContent path='img/star.svg'}
  </div>
  <div class="event-description">
    <div class="venue nowrap">
      {$venue->getName()|escape}
    </div>
    <div class="details">
      {eventtext event=$event}{if $event->getStarred()}<span class="promoted" title="{translate 'Promoted by {$siteName}' siteName=$render->getSiteName()}">♡</span>{/if}{if $event->getSong()}<span class="music">♫</span>{/if}
    </div>
  </div>
  <div class="event-context">
    <time class="date">{date_full date=$event->getFrom() timeZone=$event->getTimeZone()}</time>
    <time class="time">
      {event_time event=$event}
    </time>
    {*<div class="share">*}
    {*<span class="icon icon-share"></span>*}
    {*</div>*}
  </div>
</div>
{block name='event-after'}{/block}
