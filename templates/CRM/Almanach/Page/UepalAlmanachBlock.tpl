<p>Format : <a href="/wp-admin/admin.php?page=CiviCRM&q=civicrm%2Fuepalalmanach&layout=list">liste</a> | blocs</p>

{foreach from=$sections item=section}

  <h3>{$section.title}</h3>

  {foreach from=$section.subsections item=subsection}

    {if $subsection.title ne ""}
      <h4>{$subsection.title}</h4>
    {/if}

    <div class="view-content">
      <div class="crm-block crm-content-block">
        {foreach from=$subsection.records item=record}
          <p>
            {foreach from=$subsection.fields item=field}
              {assign var=fieldName value=$field.name}
              {if $record.$fieldName}
                {$record.$fieldName}<br>
              {/if}
            {/foreach}
            <br>
          </p>
        {/foreach}

      </div>
    </div>
  {/foreach}

{/foreach}

