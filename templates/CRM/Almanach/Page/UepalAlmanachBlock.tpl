<p>Format : <a href="uepalalmanach?layout=list">liste</a> | blocs</p>

{foreach from=$queries item=query}

  <h3>{$query.title}</h3>
  <div class="view-content">
    <div class="crm-block crm-content-block">

        {foreach from=$query.records item=record}
          <p>
              {foreach from=$query.fields item=field}
                {assign var=fieldName value=$field.name}
                {$record.$fieldName}<br>
              {/foreach}
              <br>
          </p>
        {/foreach}

    </div>
  </div>

{/foreach}

