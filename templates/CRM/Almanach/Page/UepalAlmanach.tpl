{foreach from=$queries item=query}

  <h3>{$query.title}</h3>
  <div class="view-content">
    <div class="crm-block crm-content-block">

      <table class="form-layout-compressed">
        <thead>
        <tr>
            {foreach from=$query.fields item=field}
              <th>{$field.label}</th>
            {/foreach}
        </tr>
        </thead>

        <tbody>
        {foreach from=$query.records item=record}
          <tr class="{cycle values="odd-row,even-row"}">
              {foreach from=$query.fields item=field}
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

