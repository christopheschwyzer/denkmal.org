{translate '{$siteName} - Event calendar by locals.' siteName=$render->getSite()->getName()|escape}
{if $render->getSite()->hasRegion()}
  {translate 'What\'s up in {$region} and how does it sound?' region=$render->getSite()->getRegion()->getName()|escape}
{/if}
