<?php
function viewFacultyMembersCard($facultyMembers){
  $card = "";
  if (sizeof($facultyMembers) > 0){
    $fmsWithScholar = $fmsWithScopus = $fmsWithValidatedProfile = $fmsWithOrcid = sizeof($facultyMembers);
    foreach ($facultyMembers as $fm){
      if ($fm->scholar_id == "" || $fm->scholar_id == NULL) $fmsWithScholar--;
      if ($fm->scopus_id == "" || $fm->scopus_id == NULL) $fmsWithScopus--;
      if ($fm->orcid_id == "" || $fm->orcid_id == NULL) $fmsWithOrcid--;
      if ($fm->isValidated == 0) $fmsWithValidatedProfile--;
    }

    $fmRole = $facultyMembers[0]->role;
    $card .= "<div class='row'><div class='col-md-12'><div class='card card-dark'>";
    $card .= "<div class='card-header'><h3 class='card-title'>".$fmRole->name."</h3><div class='card-tools'><button type='button' class='btn btn-tool' data-card-widget='maximize'><i class='fas fa-expand'></i><span class='sr-only'>maximize</span></button><button type='button' class='btn btn-tool' data-card-widget='collapse'><i class='fas fa-minus'></i><span class='sr-only'>collapse</span></button></div></div>";
    $card .= "<div class='card-body'>";
    /* Total stats */
    $card .= "<div class='card card-warning'><div class='card-body info-box-content'><div class='col-md-12'>";
    $card .= "<p class='text-center'><strong>"._LABEL_MEMBERS_TOTAL_STATS."</strong></p>";
    $card .= "<div class='progress-group'><span class='progress-text'>"._LABEL_MEMBERS_COUNT_SCHOLAR_PROFILES."</span><span class='float-right'><b>".$fmsWithScholar."</b>/".sizeof($facultyMembers)."</span><div class='progress progress-sm'><div class='progress-bar bg-blue' style='width: ".$fmsWithScholar*100/sizeof($facultyMembers)."%'></div></div></div>";
    $card .= "<div class='progress-group'><span class='progress-text'>"._LABEL_MEMBERS_COUNT_SCOPUS_PROFILES."</span><span class='float-right'><b>".$fmsWithScopus."</b>/".sizeof($facultyMembers)."</span><div class='progress progress-sm'><div class='progress-bar bg-orange' style='width: ".$fmsWithScopus*100/sizeof($facultyMembers)."%'></div></div></div>";
    $card .= "<div class='progress-group'><span class='progress-text'>"._LABEL_MEMBERS_COUNT_ORCID_PROFILES."</span><span class='float-right'><b>".$fmsWithOrcid."</b>/".sizeof($facultyMembers)."</span><div class='progress progress-sm'><div class='progress-bar bg-orange' style='width: ".$fmsWithOrcid*100/sizeof($facultyMembers)."%'></div></div></div>";
    $card .= "<div class='progress-group'><span class='progress-text'>"._LABEL_MEMBERS_COUNT_VALIDATED_PROFILES."</span><span class='float-right'><b>".$fmsWithValidatedProfile."</b>/".sizeof($facultyMembers)."</span><div class='progress progress-sm'><div class='progress-bar bg-grey' style='width: ".$fmsWithValidatedProfile*100/sizeof($facultyMembers)."%'></div></div></div>";
    $card .= "</div></div></div>";
    /* Table */
    $card .= "<table id='tblFacultyMembers_".$fmRole->id."' class='table table-bordered table-hover'>";
    $card .= "<thead><tr><th scope='col'>"._LABEL_FULLNAME."</th>";
    $card .= "<th scope='col'>"._LABEL_RANK."</th>";
    $card .= "<th scope='col'>"._LABEL_DEPARTMENT."</th>";
    $card .= "<th scope='col'>"._LABEL_SCHOOL."</th>";
    $card .= "<th scope='col'>"._LABEL_SCHOLAR_ID."</th>";
    $card .= "<th scope='col'>"._LABEL_SCOPUS_ID."</th>";
    $card .= "<th scope='col'>"._LABEL_ORCID_ID."</th>";
    $card .= "<th scope='col'>"._LABEL_VALIDATED_PROFILE."</th>";
    $card .= "<th scope='col'>"._LABEL_ACTION."</th>";
    $card .= "</tr></thead><tbody>";
    foreach ($facultyMembers as $fm){
      $isValidated = "";
      if ($fm->isValidated == 1) $isValidated = "Ναι";
      $card .= "<tr><td>".$fm->last_name.", ".$fm->first_name."</td>";
      $card .= "<td>".$fm->rank->full_title."</td>";
      $card .= "<td>".$fm->department->name."</td>";
      $card .= "<td>".$fm->department->school->name."</td>";
      $card .= "<td>".$fm->scholar_id."</td>";
      $card .= "<td>".$fm->scopus_id."</td>";
      if (!empty($fm->orcid_id)) {
        $card .= "<td><a href='".$fm->orcid_id."'>$fm->orcid_id</a></td>";
      } else {
        $card .= "<td></td>";
      }
      $card .= "<td class='td-validated'>".$isValidated."</td>";
      $card .= "<td><a href='editMember.php?fmid=".$fm->id."'>"._LABEL_EDIT."</a></td>";
      $card .= "</tr>";
    }

    $card .= "</tbody></table>";
    $card .= "</div>";
    $card .= "</div></div></div>";
  }
  return $card;
}




?>
