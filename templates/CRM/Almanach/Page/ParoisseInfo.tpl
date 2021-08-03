{if $paroisseId > 0}
  <p>
    Nombre de paroissiens : {$numParoissiens}<br>
    Nombre d'électeurs : {$numElecteurs}<br>
    Nombre de conseillers presbytéraux : {$numPresbyt}<br>
  </p>
  <h3>Président·e / Vice-Président·e / Trésorier·ère / Secrétaire</h3>
  <table class="report-layout display">
    <thead>
    <tr>
      <th>Rôle</th>
      <th>Identité</th>
      <th>Mandat actuel : début</th>
      <th>Mandat actuel : fin</th>
      <th>Mandats antérieurs</th>
      <th>Courriel</th>
      <th>Téléphone</th>
      <th>Adresse</th>
      <th>Date de naissance</th>
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
        <td>{$row.phone}</td>
        <td>{$row.address}</td>
        <td>{$row.birth_date}</td>
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
      <th>Téléphone</th>
      <th>Adresse</th>
      <th>Date de naissance</th>
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
        <td>{$row.phone}</td>
        <td>{$row.address}</td>
        <td>{$row.birth_date}</td>
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
      <th>Téléphone</th>
      <th>Adresse</th>
      <th>Date de naissance</th>
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
        <td>{$row.phone}</td>
        <td>{$row.address}</td>
        <td>{$row.birth_date}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <p>&nbsp;</p>

  <h3>Membres invités</h3>
  <table class="report-layout display">
    <thead>
    <tr>
      <th>Rôle</th>
      <th>Identité</th>
      <th>Mandat actuel : début</th>
      <th>Mandat actuel : fin</th>
      <th>Mandats antérieurs</th>
      <th>Courriel</th>
      <th>Téléphone</th>
      <th>Adresse</th>
      <th>Date de naissance</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$invitedMembersArr item=row}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.role}</td>
        <td>{$row.name}</td>
        <td>{$row.start_date}</td>
        <td>{$row.end_date}</td>
        <td>{$row.ex_relationships}</td>
        <td>{$row.email}</td>
        <td>{$row.phone}</td>
        <td>{$row.address}</td>
        <td>{$row.birth_date}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <p>&nbsp;</p>

  <p>&nbsp;</p>
{/if}
