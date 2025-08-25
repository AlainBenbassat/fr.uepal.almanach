<p>Format : liste | <a href="/wp-admin/admin.php?page=CiviCRM&q=civicrm%2Fuepalalmanach&layout=block">blocs</a></p>

{foreach from=$sections item=section}

  <h3>{$section.title}</h3>

  {foreach from=$section.subsections item=subsection}

    {if $subsection.title ne ""}
      <h4>{$subsection.title}</h4>
    {/if}

    <div class="view-content">
      <div class="crm-block crm-content-block">

        <table class="form-layout-compressed">
          <thead>
          <tr>
              {foreach from=$subsection.fields item=field}
                <th>{$field.label}</th>
              {/foreach}
          </tr>
          </thead>

          <tbody>
          {foreach from=$subsection.records item=record}
            <tr class="{cycle values="odd-row,even-row"}">
                {foreach from=$subsection.fields item=field}
                    {assign var=fieldName value=$field.name}
                  <td>{$record.$fieldName}</td>
                {/foreach}
            </tr>
          {/foreach}
          </tbody>
        </table>

      </div>
    </div>
  {/foreach}

{/foreach}


