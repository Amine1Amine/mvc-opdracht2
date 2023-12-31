<?php

class Instructeur extends BaseController
{
    private $instructeurModel;

    public function __construct()
    {
        $this->instructeurModel = $this->model('InstructeurModel');
    }

    public function overzichtInstructeur()
    {
        $result = $this->instructeurModel->getInstructeurs();

        //  var_dump($result);

        $rows = "";
        $amount = 0;
        foreach ($result as $instructeur) {
            $date = date_create($instructeur->DatumInDienst);
            $formattedDate = date_format($date, "d-m-Y");
            $amount++;
            // var_dump($instructeur);
            $rows .= "<tr>
                        <td>$instructeur->Voornaam</td>
                        <td>$instructeur->Tussenvoegsel</td>
                        <td>$instructeur->Achternaam</td>
                        <td>$instructeur->Mobiel</td>
                        <td>$formattedDate</td>            
                        <td>$instructeur->AantalSterren</td> 
                        <td>
                            <a href='" . URLROOT . "/instructeur/overzichtvoertuigen/$instructeur->Id'>
                                <span class='material-symbols-outlined'>
                                directions_car
                                </span>
                            </a>
                        </td>    
                        <td>
                        <a href='" . URLROOT . "/instructeur/overzichtinstructeur/$instructeur->Id'>";

                        if ($instructeur->IsActief) {
                            $rows .= "<a href='" . URLROOT . "/instructeur/changeIsActief/$instructeur->Id'> <span class='material-symbols-outlined'>
                            <i class='bi bi-hand-thumbs-up'></i>
                                        </span></a>";
                        } else {
                            $rows .= "<a href='" . URLROOT . "/instructeur/changeIsNotActief/$instructeur->Id'> <span class='material-symbols-outlined'>
                            <i class='bi bi-bandaid'></i>
                                        </span></a>";
                        }
            $rows .= "
                        </a>
                    </td>
                    <td>
                    <a href='" . URLROOT . "/instructeur/deleteInstructeur/$instructeur->Id'>
                        <span class='material-symbols-outlined'>
                        <i class='bi bi-person-x'></i>
                        </span>
                    </a>
                </td>   
                      </tr>";
        }

        $data = [
            'title' => 'Instructeurs in dienst',
            'rows' => $rows,
            'amount' => $amount
        ];  

        $this->view('Instructeur/overzichtinstructeur', $data);
    }


    public function changeIsActief($instructeurId) 
    {
        $changeIsActief = $this->instructeurModel->changeIsActief($instructeurId);

        // var_dump($changeIsActief);
        $this->overzichtInstructeur();
    }

    public function changeIsNotActief($instructeurId) 
    {
        $changeIsActief = $this->instructeurModel->changeIsNotActief($instructeurId);
    
         // var_dump($changeIsNotActief);
        $this->overzichtInstructeur();
    

    }

    public function deleteInstructeur($instructeurId) 
    {
        $isDeleted = $this->instructeurModel->deleteInstructeur($instructeurId);
    

            echo "<div id='deleteMessage'>Instructor met ID: $instructeurId is definitief verwijdert en all zijn eerder toegewezen voertuigen zijn vrijgegeven</div>";
            echo "<script>
                    setTimeout(function() {
                        document.getElementById('deleteMessage').style.display = 'none';
                        window.location.href = '{$_SERVER['HTTP_REFERER']}';
                    }, 2000); 
                  </script>";

    }
    
    
    

    public function overzichtVoertuigen($Id, $Message = null)
    {
        $result = $this->instructeurModel->getInstructeurs();
        foreach ($result as $person) {
            if ($person->Id == $Id) {
                $instructeur = $person;
            }
        }

        $result = $this->instructeurModel->getToegewezenVoertuigen($Id);
        if ($result != null) {
            $tableRows = "";
            foreach ($result as $voertuig) {
                $tableRows .= "<tr>
                                <td>$voertuig->TypeVoertuig</td>
                                <td>$voertuig->Type</td>
                                <td>$voertuig->Kenteken</td>
                                <td>$voertuig->Bouwjaar</td>
                                <td>$voertuig->Brandstof</td>
                                <td>$voertuig->RijbewijsCategorie</td>
                                <th>
                                    <a href='" . URLROOT . "/Voertuig/editVoertuig/" . $voertuig->Id . "'>
                                        <span class='material-symbols-outlined'>
                                            edit
                                        </span>
                                    </a>
                                </th>
                                <th>
                                    <a href='" . URLROOT . "/instructeur/deleteCar/$voertuig->Id/$instructeur->Id'>
                                        <span class='material-symbols-outlined'>
                                            delete
                                        </span>
                                    </a>
                                </th>
                               </tr> ";
            };
        } else {
            $tableRows = "<tr><td colspan='6'>Nog geen voertuigen toegewezen</td></tr>";
        }

        $data = [
            'title' => 'Door instructeur gebruikte voertuigen',
            'tableRows' => $tableRows,
            'personData' => $instructeur,
            'message' => $Message
        ];

        $this->view('Instructeur/overzichtVoertuigen', $data);
    }

    public function beschikbarenVoertuigen($Id)
    {
        $result = $this->instructeurModel->getInstructeurs();
        foreach ($result as $person) {
            if ($person->Id == $Id) {
                $instructeur = $person;
            }
        }

        $result = $this->instructeurModel->getVrijeVoertuigen($Id);
        if ($result != null) {
            $tableRows = "";
            foreach ($result as $voertuig) {
                $tableRows .= "<tr>
                                <td>$voertuig->TypeVoertuig</td>
                                <td>$voertuig->Type</td>
                                <td>$voertuig->Kenteken</td>
                                <td>$voertuig->Bouwjaar</td>
                                <td>$voertuig->Brandstof</td>
                                <td>$voertuig->RijbewijsCategorie</td>
                                <td>
                                    <a href='" . URLROOT . "/instructeur/beschikbarenVoertuigen/" . $instructeur->Id . "?Update=true&CarId=$voertuig->Id'>
                                        <span class='material-symbols-outlined'>
                                            add_box
                                        </span>
                                    </a>
                                </td>
                               </tr> ";
            };
        } else {
            $tableRows = "<tr><td colspan='7'>Geen vrije voertuigen</td></tr>";
        }

        $data = [
            'title' => 'Alle beschikbaren voertuigen',
            'tableRows' => $tableRows,
            'personData' => $instructeur
        ];

        $this->view('Instructeur/beschikbarenVoertuigen', $data);
    }

    public function updateVoertuigen($CarId, $PersonId)
    {
        $this->instructeurModel->addCarToInstructeur($CarId, $PersonId);

        header("Location: " . URLROOT . "/instructeur/overzichtVoertuigen/$PersonId/Voertuig%20toegevoegd");
    }

    public function deleteCar($CarId, $PersonId)
    {
        $this->instructeurModel->deleteCarFromInstructeur($CarId, $PersonId);

        header("Location: " . URLROOT . "/instructeur/overzichtVoertuigen/$PersonId/Voertuig%20verwijderd");
    }
}
