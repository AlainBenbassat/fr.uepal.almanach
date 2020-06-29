{if $paroisseId > 0}
  <p>
    Nombre de paroissiens : {$numParoissiens}<br>
    Nombre d'électeurs : {$numElecteurs}<br>
  </p>
  <h3>Président·e / Vice-Président·e / Trésorier.ère / Secrétaire</h3>
  <table class="report-layout display">
    <thead>
    <tr>
      <th>Rôle</th>
      <th>Identité</th>
      <th>Mandat actuel : début</th>
      <th>Mandat actuel : fin</th>
      <th>Mandats antérieurs</th>
      <th>Courriel</th>
      <th>Téléphone portable</th>
      <th>Téléphone fixe</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$managementArr item=row}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.role}</td>
        <td>{$row.name}</td>
        <td>{$row.start_date}</td>
        <td>{$row.end_date}</td>
        <td>{$row.ex_relationships}</td>
        <td>{$row.email}</td>
        <td>{$row.phone_fixed}</td>
        <td>{$row.phone_mobile}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <p>&nbsp;</p>

  <h3>Membres de droit</h3>
  <table class="report-layout display">
    <thead>
    <tr>
      <th>Rôle</th>
      <th>Identité</th>
      <th>Mandat actuel : début</th>
      <th>Mandat actuel : fin</th>
      <th>Mandats antérieurs</th>
      <th>Courriel</th>
      <th>Téléphone portable</th>
      <th>Téléphone fixe</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$rightMembersArr item=row}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.role}</td>
        <td>{$row.name}</td>
        <td>{$row.start_date}</td>
        <td>{$row.end_date}</td>
        <td>{$row.ex_relationships}</td>
        <td>{$row.email}</td>
        <td>{$row.phone_fixed}</td>
        <td>{$row.phone_mobile}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <p>&nbsp;</p>

  <h3>Membres élus</h3>
  <table class="report-layout display">
    <thead>
    <tr>
      <th>Rôle</th>
      <th>Identité</th>
      <th>Mandat actuel : début</th>
      <th>Mandat actuel : fin</th>
      <th>Mandats antérieurs</th>
      <th>Courriel</th>
      <th>Téléphone portable</th>
      <th>Téléphone fixe</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$electedMembersArr item=row}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.role}</td>
        <td>{$row.name}</td>
        <td>{$row.start_date}</td>
        <td>{$row.end_date}</td>
        <td>{$row.ex_relationships}</td>
        <td>{$row.email}</td>
        <td>{$row.phone_fixed}</td>
        <td>{$row.phone_mobile}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <p>&nbsp;</p>

{/if}
