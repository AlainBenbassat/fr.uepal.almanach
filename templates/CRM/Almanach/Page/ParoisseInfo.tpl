{* Level 1: Inspection / Consistoire réformé *}
{foreach from=$hierarchy key=inspectionName item=inspectionDetails}
    {if !empty($inspectionName)}
      <h1>{$inspectionName}</h1>
      <hr>
    {/if}

    {* Level 2: Consistoire or dummy for Consistoire réformé *}
    {foreach from=$inspectionDetails key=consistoireName item=consistoireDetails}
      {if !empty($consistoireName)}
        <h1>{$consistoireName}</h1>
        <p>&nbsp;</p>
      {/if}

      {* Level 3: Paroisse *}
      {foreach from=$consistoireDetails key=paroisseName item=paroisseDetails}
        <h2>Paroisse : {$paroisseName}</h2>

        <p>
          Nombre de paroissiens : {$paroisseDetails.numParoissiens}<br>
          Nombre d'électeurs : {$paroisseDetails.numElecteurs}<br>
          Nombre de conseillers presbytéraux : {$paroisseDetails.numPresbyt}<br>
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
          {foreach from=$paroisseDetails.managementArr item=row}
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
          {foreach from=$paroisseDetails.rightMembersArr item=row}
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
          {foreach from=$paroisseDetails.electedMembersArr item=row}
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
          {foreach from=$paroisseDetails.invitedMembersArr item=row}
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

      {/foreach}
    {/foreach}

{/foreach}



