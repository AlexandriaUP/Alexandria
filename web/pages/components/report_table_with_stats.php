<?php
  $report_previous_year = $report_year - 1;
  $report_2_years_before = $report_year - 2;
  $report_3_years_before = $report_year - 3;

  $card_class = "card-primary";
  if ($tbl_provider_id == "scopus") $card_class = "card-warning";

  $unit_total_cols = 15;
  if ($tbl_provider_id == "scopus") $unit_total_cols = 17;

  $unit_sub_cols = 16;
  if ($tbl_provider_id == "scopus") $unit_sub_cols = 18;

  $fm_cols = 19;
  if ($tbl_provider_id == "scopus") $fm_cols = 21;

?>

<div class="col-12 col-sm-12">
  <div class="card <?php echo $card_class; ?> card-tabs">
    <div class="card-header p-0 pt-1">
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand" style="color: #000;"></i><span class='sr-only'>maximize</span></button>
        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus" style="color: #000;"></i><span class='sr-only'>collapse</span></button>
      </div>
      <ul class="nav nav-tabs" id="<?php echo $tPrefix;?>-provider-tab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="<?php echo $tPrefix;?>-unitStats-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-unitStats-tabContent" role="tab" aria-controls="<?php echo $tPrefix;?>-unitStats" aria-selected="true"><?php echo _TABLE_STATS_UNIVERSITY; ?></a>
        </li>
        <?php if ($type != "department_report"){?>
        <li class="nav-item">
          <a class="nav-link" id="<?php echo $tPrefix;?>-subUnitStats-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-subUnitStats-tabContent" role="tab" aria-controls="<?php echo $tPrefix;?>-subUnitStats" aria-selected="false">
          <?php
            if ($type == "university_report") echo _TABLE_STATS_SCHOOL;
            else if ($type == "school_report") echo _TABLE_STATS_DEPARTMENT;
          ?>
          </a>
        </li>
        <?php } ?>
        <li class="nav-item">
          <a class="nav-link" id="<?php echo $tPrefix;?>-rankStats-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-rankStats-tabContent" role="tab" aria-controls="<?php echo $tPrefix;?>-rankStats" aria-selected="false"><?php echo _TABLE_STATS_RANK; ?></a>
        </li>
        <?php if( $_SESSION["role"] == "fm" &&  ( ( !isset($_GET['sid']) && !isset($_GET['did']) ) || (isset($_GET['sid']) && $_GET['sid']==$_SESSION["member_school"])  || (isset($_GET['did']) && $_GET['did']==$_SESSION["member_dept"]) )  ) { ?>
        <li class="nav-item">
          <a class="nav-link" id="<?php echo $tPrefix;?>-facultyMemberStats-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-facultyMemberStats-tabContent" role="tab" aria-controls="<?php echo $tPrefix;?>-facultyMemberStats" aria-selected="false"><?php echo _TABLE_MY_DATA; ?></a>
        </li>
        <?php } else if ($_SESSION["role"] == "admin") {?>
        <li class="nav-item">
          <a class="nav-link" id="<?php echo $tPrefix;?>-facultyMemberStats-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-facultyMemberStats-tabContent" role="tab" aria-controls="<?php echo $tPrefix;?>-facultyMemberStats" aria-selected="false"><?php echo _TABLE_STATS_FACULTY_MEMBER; ?></a>
        </li>
        <?php } ?>
        <?php if ($type == "university_report" && $tbl_role_id == "all") {?>
        <li class="nav-item">
                  <a class="nav-link" id="<?php echo $tPrefix;?>-top210-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-top210-tabContent" role="tab" aria-controls="<?php echo $tPrefix;?>-top210" aria-selected="false"><?php echo _TABLE_STATS_TOP210; ?></a>
                </li>
        <?php } ?>
      </ul>
    </div><!-- /.card-header -->
    <div class="card-body">
      <div class="tab-content" id="custom-tabs-two-tabContent">
        <div class="tab-pane fade active show" id="<?php echo $tPrefix;?>-unitStats-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-unitStats-tabContent">
            <div class="card <?php echo $card_class; ?> card-outline card-outline-tabs">
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="<?php echo $tPrefix;?>-unitStats-meanScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-unitStats-meanScores-tabContent" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true"><?php echo _TABLE_STATS_M_AVG; ?></a>
  							  </li>
  							  <li class="nav-item">
  								  <a class="nav-link" id="<?php echo $tPrefix;?>-unitStats-q1Scores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-unitStats-q1Scores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q1; ?></a>
  							  </li>
  							  <li class="nav-item">
  								  <a class="nav-link" id="<?php echo $tPrefix;?>-unitStats-medianScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-unitStats-medianScores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q2; ?></a>
  							  </li>
  							  <li class="nav-item">
  								  <a class="nav-link" id="<?php echo $tPrefix;?>-unitStats-q3Scores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-unitStats-q3Scores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q3; ?></a>
  							  </li>
  								<li class="nav-item">
  								  <a class="nav-link" id="<?php echo $tPrefix;?>-unitStats-totalScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-unitStats-totalScores-tabContent" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true"><?php echo _TABLE_STATS_M_SUM; ?></a>
  							  </li>
                </ul>
              </div> <!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                  <div class="tab-pane fade active show" id="<?php echo $tPrefix;?>-unitStats-meanScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-unitStats-meanScores-tabContent">
                    <table id="tbl<?php echo $tPrefix;?>UnitMeanScores" class="table table-bordered table-hover">
                      <thead>
    										<tr>
    											<th colspan="<?php echo $unit_total_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_UNIVERSITY; ?> / <?php echo _TABLE_STATS_M_AVG; ?></th>
    										</tr>
    										<tr>
    											<td colspan="1" class="mergedCell"></td>
    											<th colspan="<?php echo intval($unit_total_cols - 11);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
    											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
    										</tr>
    										<tr>
    											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
    											<th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                          <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                          <?php } ?>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
    											<th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    										</tr>
    									</thead>
                      <tbody>
    										<tr>
    											<td><?php echo sizeof($metricsPerUnit[0]->publications); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->publications), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->citations), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->hindex), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->i10index), 0); ?></td>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <td><?php echo round(getMeanScore($metricsPerUnit[0]->pubs_q12), 0); ?></td>
      										<td><?php echo round(getMeanScore($metricsPerUnit[0]->pubs_q1234), 0); ?></td>
                          <?php } ?>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->publications_5y), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->citations_5y), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->hindex_5y), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->i10index_5y), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->publications_3_years_before), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->citations_3_years_before), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->publications_2_years_before), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->citations_2_years_before), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->publications_previous_year), 0); ?></td>
    											<td><?php echo round(getMeanScore($metricsPerUnit[0]->citations_previous_year), 0); ?></td>
    										</tr>
    									</tbody>
                    </table>
                  </div><!-- /.tab-pane -->
                  <div class="tab-pane fade" id="<?php echo $tPrefix;?>-unitStats-medianScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-unitStats-medianScores-tabContent">
                    <table id="tbl<?php echo $tPrefix;?>UnitMedianScores" class="table table-bordered table-hover">
                      <thead>
    										<tr>
    											<th colspan="<?php echo $unit_total_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_UNIVERSITY; ?> / <?php echo _TABLE_STATS_M_Q2; ?></th>
    										</tr>
    										<tr>
    											<td colspan="1" class="mergedCell"></td>
    											<th colspan="<?php echo intval($unit_total_cols - 11);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
    											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
    										</tr>
    										<tr>
    											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                          <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                          <?php } ?>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    										</tr>
    									</thead>
                      <tbody>
    										<tr>
    											<td><?php echo sizeof($metricsPerUnit[0]->publications); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->publications), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->citations), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->hindex), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->i10index), 1); ?></td>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <td><?php echo round(getMedian($metricsPerUnit[0]->pubs_q12), 0); ?></td>
      										<td><?php echo round(getMedian($metricsPerUnit[0]->pubs_q1234), 0); ?></td>
                          <?php } ?>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->publications_5y), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->citations_5y), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->hindex_5y), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->i10index_5y), 1); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->publications_3_years_before), 0); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->citations_3_years_before), 0); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->publications_2_years_before), 0); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->citations_2_years_before), 0); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->publications_previous_year), 0); ?></td>
    											<td><?php echo round(getMedian($metricsPerUnit[0]->citations_previous_year), 0); ?></td>
    										</tr>
    									</tbody>
                    </table>
                  </div><!-- ./tab-pane -->
                  <div class="tab-pane fade" id="<?php echo $tPrefix;?>-unitStats-q1Scores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-unitStats-q1Scores-tabContent">
                    <table id="tbl<?php echo $tPrefix;?>UnitQ1Scores" class="table table-bordered table-hover">
    									<thead>
    										<tr>
    											<th colspan="<?php echo $unit_total_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_UNIVERSITY; ?> / <?php echo _TABLE_STATS_M_Q1; ?></th>
    										</tr>
    										<tr>
    											<td colspan="1" class="mergedCell"></td>
    											<th colspan="<?php echo intval($unit_total_cols - 11);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
    											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
    										</tr>
    										<tr>
    											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                          <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                          <?php } ?>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    										</tr>
    									</thead>
    									<tbody>
    										<tr>
    											<td><?php echo sizeof($metricsPerUnit[0]->publications); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->publications), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->citations), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->hindex), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->i10index), 1); ?></td>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <td><?php echo round(getQ1Score($metricsPerUnit[0]->pubs_q12), 0); ?></td>
      										<td><?php echo round(getQ1Score($metricsPerUnit[0]->pubs_q1234), 0); ?></td>
                          <?php } ?>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->publications_5y), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->citations_5y), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->hindex_5y), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->i10index_5y), 1); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->publications_3_years_before), 0); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->citations_3_years_before), 0); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->publications_2_years_before), 0); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->citations_2_years_before), 0); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->publications_previous_year), 0); ?></td>
    											<td><?php echo round(getQ1Score($metricsPerUnit[0]->citations_previous_year), 0); ?></td>
    										</tr>
    									</tbody>
    								</table>
                  </div><!-- ./tab-pane -->
                  <div class="tab-pane fade" id="<?php echo $tPrefix;?>-unitStats-q3Scores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-unitStats-q3Scores-tabContent">
                    <table id="tbl<?php echo $tPrefix;?>UnitQ3Scores" class="table table-bordered table-hover">
    									<thead>
    										<tr>
    											<th colspan="<?php echo $unit_total_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_UNIVERSITY; ?> / <?php echo _TABLE_STATS_M_Q3; ?></th>
    										</tr>
    										<tr>
    											<td colspan="1" class="mergedCell"></td>
    											<th colspan="<?php echo intval($unit_total_cols - 11);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
    											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
    										</tr>
    										<tr>
    											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                          <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                          <?php } ?>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
    											<th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    										</tr>
    									</thead>
    									<tbody>
    										<tr>
    											<td><?php echo sizeof($metricsPerUnit[0]->publications); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->publications), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->citations), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->hindex), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->i10index), 1); ?></td>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <td><?php echo round(getQ1Score($metricsPerUnit[0]->pubs_q12), 0); ?></td>
      										<td><?php echo round(getQ1Score($metricsPerUnit[0]->pubs_q1234), 0); ?></td>
                          <?php } ?>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->publications_5y), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->citations_5y), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->hindex_5y), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->i10index_5y), 1); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->publications_3_years_before), 0); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->citations_3_years_before), 0); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->publications_2_years_before), 0); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->citations_2_years_before), 0); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->publications_previous_year), 0); ?></td>
    											<td><?php echo round(getQ3Score($metricsPerUnit[0]->citations_previous_year), 0); ?></td>
    										</tr>
    									</tbody>
    								</table>
                  </div><!-- ./tab-pane -->
                  <div class="tab-pane fade" id="<?php echo $tPrefix;?>-unitStats-totalScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-unitStats-totalScores-tabContent">
                    <table id="tbl<?php echo $tPrefix;?>UnitTotalScores" class="table table-bordered table-hover">
    									<thead>
    										<tr>
    											<th colspan="<?php echo intval($unit_total_cols - 4);?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_UNIVERSITY; ?> / <?php echo _TABLE_STATS_M_SUM; ?></th>
    										</tr>
    										<tr>
    											<td colspan="1" class="mergedCell"></td>
    											<th colspan="<?php echo intval($unit_total_cols - 13);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
    											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
    										</tr>
    										<tr>
    											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                          <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                          <?php } ?>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
    											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
    										</tr>
    									</thead>
    									<tbody>
    										<tr>
    											<td><?php echo sizeof($metricsPerUnit[0]->publications); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->publications), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->citations), 0); ?></td>
                          <?php if ($tbl_provider_id == "scopus") { ?>
                          <td><?php echo round(getTotal($metricsPerUnit[0]->pubs_q12), 0); ?></td>
      										<td><?php echo round(getTotal($metricsPerUnit[0]->pubs_q1234), 0); ?></td>
                          <?php } ?>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->publications_5y), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->citations_5y), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->publications_3_years_before), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->citations_3_years_before), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->publications_2_years_before), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->citations_2_years_before), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->publications_previous_year), 0); ?></td>
    											<td><?php echo round(getTotal($metricsPerUnit[0]->citations_previous_year), 0); ?></td>
    										</tr>
    									</tbody>
    								</table>
                  </div><!-- ./tab-pane -->
                </div><!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div> <!-- /.card -->
        </div><!-- /.tab-pane -->
        <div class="tab-pane fade" id="<?php echo $tPrefix;?>-subUnitStats-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-subUnitStats-tabContent">
          <div class="card <?php echo $card_class; ?> card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
							  <li class="nav-item">
								   <a class="nav-link active" id="<?php echo $tPrefix;?>-subUnitStats-meanScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-subUnitStats-meanScores-tabContent" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true"><?php echo _TABLE_STATS_M_AVG; ?></a>
							  </li>
							  <li class="nav-item">
								   <a class="nav-link" id="<?php echo $tPrefix;?>-subUnitStats-q1Scores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-subUnitStats-q1Scores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q1; ?></a>
							  </li>
							  <li class="nav-item">
								   <a class="nav-link" id="<?php echo $tPrefix;?>-subUnitStats-medianScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-subUnitStats-medianScores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q2; ?></a>
							  </li>
							  <li class="nav-item">
								   <a class="nav-link" id="<?php echo $tPrefix;?>-subUnitStats-q3Scores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-subUnitStats-q3Scores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Tιμή Q3</a>
							  </li>
								<li class="nav-item">
								   <a class="nav-link" id="<?php echo $tPrefix;?>-subUnitStats-totalScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-subUnitStats-totalScores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_SUM; ?></a>
							  </li>
							</ul>
            </div><!-- /.card-header -->
            <div class="card-body">
              <div class="tab-content" id="custom-tabs-four-tabContent">
                <div class="tab-pane fade active show" id="<?php echo $tPrefix;?>-subUnitStats-meanScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-subUnitStats-meanScores-tabContent">
                  <table id="tbl<?php echo $tPrefix;?>SubUnitMeanScores" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / Στατιστικά ανά <?php
                        if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
                        else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
                        ?> / <?php echo _TABLE_STATS_M_AVG; ?></th>
                      </tr>
                      <tr>
                        <td colspan="2" class="mergedCell"></td>
                        <th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
                        <th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
                        <th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
                        <th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
                        <th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
                      </tr>
                      <tr>
                        <th scope="col">
                        <?php
                        if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
                        else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
                        ?>
                        </th>
                        <th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                        <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                        <?php } ?>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                        <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $suid = "sid";
                      if ($type=="school_report") $suid = "did";
                      if (is_countable($metricsPerSubUnit) && sizeof($metricsPerSubUnit) > 0){
                      foreach ($metricsPerSubUnit as $mps){
                        if ( !empty($mps->publications)) {
                      ?>
                      <tr>
                        <td><?php echo "<a href='report.php?rid=$report_id&$suid=".$mps->unit->id."'>".$mps->unit->name."</a>"; ?></td>
                        <td><?php echo sizeof($mps->publications); ?></td>
                        <td><?php echo round(getMeanScore($mps->publications), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->citations), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->hindex), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->i10index), 0); ?></td>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <td><?php echo round(getMeanScore($mps->pubs_q12), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->pubs_q1234), 0); ?></td>
                        <?php } ?>
                        <td><?php echo round(getMeanScore($mps->publications_5y), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->citations_5y), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->hindex_5y), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->i10index_5y), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->publications_3_years_before), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->citations_3_years_before), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->publications_2_years_before), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->citations_2_years_before), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->publications_previous_year), 0); ?></td>
                        <td><?php echo round(getMeanScore($mps->citations_previous_year), 0); ?></td>
                      </tr>
                      <?php }}} ?>
                    </tbody>
                  </table>
                </div><!-- /.tab-pane -->
                <div class="tab-pane fade" id="<?php echo $tPrefix;?>-subUnitStats-medianScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-subUnitStats-medianScores-tabContent">
                  <table id="tbl<?php echo $tPrefix;?>SubUnitMedianScores" class="table table-bordered table-hover">
  									<thead>
  										<tr>
  											<th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / Στατιστικά ανά <?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?> / <?php echo _TABLE_STATS_M_Q2; ?></th>
  										</tr>
  										<tr>
  											<td colspan="2" class="mergedCell"></td>
  											<th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
  											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
  										</tr>
  										<tr>
  											<th scope="col">
  											<?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?>
  											</th>
  											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                        <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                        <?php } ?>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  										</tr>
  									</thead>
  									<tbody>
  										<?php
										if (is_countable($metricsPerSubUnit) && sizeof($metricsPerSubUnit) > 0){
  										foreach ($metricsPerSubUnit as $mps){
  											if ( !empty($mps->publications)) {
  										?>
  										<tr>
  											<td><?php echo "<a href='report.php?rid=$report_id&$suid=".$mps->unit->id."'>".$mps->unit->name."</a>"; ?></td>
  											<td><?php echo sizeof($mps->publications); ?></td>
  											<td><?php echo round(getMedian($mps->publications), 1); ?></td>
  											<td><?php echo round(getMedian($mps->citations), 1); ?></td>
  											<td><?php echo round(getMedian($mps->hindex), 1); ?></td>
  											<td><?php echo round(getMedian($mps->i10index), 1); ?></td>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <td><?php echo round(getMedian($mps->pubs_q12), 0); ?></td>
                        <td><?php echo round(getMedian($mps->pubs_q1234), 0); ?></td>
                        <?php } ?>
  											<td><?php echo round(getMedian($mps->publications_5y), 1); ?></td>
  											<td><?php echo round(getMedian($mps->citations_5y), 1); ?></td>
  											<td><?php echo round(getMedian($mps->hindex_5y), 1); ?></td>
  											<td><?php echo round(getMedian($mps->i10index_5y), 1); ?></td>
  											<td><?php echo round(getMedian($mps->publications_3_years_before), 1); ?></td>
  											<td><?php echo round(getMedian($mps->citations_3_years_before), 1); ?></td>
  											<td><?php echo round(getMedian($mps->publications_2_years_before), 1); ?></td>
  											<td><?php echo round(getMedian($mps->citations_2_years_before), 1); ?></td>
  											<td><?php echo round(getMedian($mps->publications_previous_year), 1); ?></td>
  											<td><?php echo round(getMedian($mps->citations_previous_year), 1); ?></td>
  										</tr>
  										<?php } } }?>
  									</tbody>
  								</table>
                </div><!-- /.tab-pane -->
                <div class="tab-pane fade" id="<?php echo $tPrefix;?>-subUnitStats-q1Scores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-subUnitStats-q1Scores-tabContent">
                  <table id="tbl<?php echo $tPrefix;?>SubUnitQ1Scores" class="table table-bordered table-hover">
  									<thead>
  										<tr>
  											<th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / Στατιστικά ανά <?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?> / Tιμή Q1</th>
  										</tr>
  										<tr>
  											<td colspan="2" class="mergedCell"></td>
  											<th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
  											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
  										</tr>
  										<tr>
  											<th scope="col">
  											<?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?>
  											</th>
  											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                        <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                        <?php } ?>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  										</tr>
  									</thead>
  									<tbody>
  										<?php
										if (is_countable($metricsPerSubUnit) && sizeof($metricsPerSubUnit) > 0){
  										foreach ($metricsPerSubUnit as $mps){
  											if ( !empty($mps->publications)) {
  										?>
  										<tr>
  											<td><?php echo "<a href='report.php?rid=$report_id&$suid=".$mps->unit->id."'>".$mps->unit->name."</a>"; ?></td>
  											<td><?php echo sizeof($mps->publications); ?></td>
  											<td><?php echo round(getQ1Score($mps->publications), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->citations), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->hindex), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->i10index), 1); ?></td>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <td><?php echo round(getQ1Score($mps->pubs_q12), 0); ?></td>
                        <td><?php echo round(getQ1Score($mps->pubs_q1234), 0); ?></td>
                        <?php } ?>
  											<td><?php echo round(getQ1Score($mps->publications_5y), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->citations_5y), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->hindex_5y), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->i10index_5y), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->publications_3_years_before), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->citations_3_years_before), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->publications_2_years_before), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->citations_2_years_before), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->publications_previous_year), 1); ?></td>
  											<td><?php echo round(getQ1Score($mps->citations_previous_year), 1); ?></td>
  										</tr>
                    <?php } } }?>
  									</tbody>
  								</table>
                </div><!-- /.tab-pane -->
                <div class="tab-pane fade" id="<?php echo $tPrefix;?>-subUnitStats-q3Scores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-subUnitStats-q3Scores-tabContent">
                  <table id="tbl<?php echo $tPrefix;?>SubUnitQ3Scores" class="table table-bordered table-hover">
  									<thead>
  										<tr>
  											<th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / Στατιστικά ανά <?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?> / <?php echo _TABLE_STATS_M_Q3; ?></th>
  										</tr>
  										<tr>
  											<td colspan="2" class="mergedCell"></td>
  											<th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
  											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
  										</tr>
  										<tr>
  											<th scope="col">
  											<?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?>
  											</th>
  											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                        <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                        <?php } ?>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                        <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  										</tr>
  									</thead>
  									<tbody>
  										<?php
										if (is_countable($metricsPerSubUnit) && sizeof($metricsPerSubUnit) > 0){
  										foreach ($metricsPerSubUnit as $mps){
  											if ( !empty($mps->publications)) {
  										?>
  										<tr>
  											<td><?php echo "<a href='report.php?rid=$report_id&$suid=".$mps->unit->id."'>".$mps->unit->name."</a>"; ?></td>
  											<td><?php echo sizeof($mps->publications); ?></td>
  											<td><?php echo round(getQ3Score($mps->publications), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->citations), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->hindex), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->i10index), 1); ?></td>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <td><?php echo round(getQ3Score($mps->pubs_q12), 0); ?></td>
                        <td><?php echo round(getQ3Score($mps->pubs_q1234), 0); ?></td>
                        <?php } ?>
  											<td><?php echo round(getQ3Score($mps->publications_5y), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->citations_5y), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->hindex_5y), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->i10index_5y), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->publications_3_years_before), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->citations_3_years_before), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->publications_2_years_before), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->citations_2_years_before), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->publications_previous_year), 1); ?></td>
  											<td><?php echo round(getQ3Score($mps->citations_previous_year), 1); ?></td>
  										</tr>
                      <?php } } } ?>
  									</tbody>
  								</table>
                </div><!-- /.tab-pane -->
                <div class="tab-pane fade" id="<?php echo $tPrefix;?>-subUnitStats-totalScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-subUnitStats-totalScores-tabContent">
                  <table id="tbl<?php echo $tPrefix;?>SubUnitTotalScores" class="table table-bordered table-hover">
  									<thead>
  										<tr>
  											<th colspan="<?php echo intval($unit_sub_cols - 4);?>" class="mergedCell"><?php echo $unit->name;?> / Στατιστικά ανά <?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;
  											?> / <?php echo _TABLE_STATS_M_SUM; ?></th>
  										</tr>
  										<tr>
  											<td colspan="2" class="mergedCell"></td>
  											<th colspan="<?php echo intval($unit_sub_cols - 14);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
  											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
  										</tr>
  										<tr>
  											<th scope="col">
  											<?php
  											if ($type == "university_report") echo _TABLE_STATS_TITLE_SCHOOL;
  											else if ($type == "school_report") echo _TABLE_STATS_TITLE_DEPARTMENT;;

  											?>
  											</th>
  											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                        <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                        <?php } ?>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
  											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
  										</tr>
  									</thead>
  									<tbody>
  										<?php
										if (is_countable($metricsPerSubUnit) && sizeof($metricsPerSubUnit) > 0){
  										foreach ($metricsPerSubUnit as $mps){
  											if ( !empty($mps->publications)) {
  										?>
  										<tr>
  											<td><?php echo "<a href='report.php?rid=$report_id&$suid=".$mps->unit->id."'>".$mps->unit->name."</a>"; ?></td>
  											<td><?php echo sizeof($mps->publications); ?></td>
  											<td><?php echo round(getTotal($mps->publications), 1); ?></td>
  											<td><?php echo round(getTotal($mps->citations), 1); ?></td>
                        <?php if ($tbl_provider_id == "scopus") { ?>
                        <td><?php echo round(getTotal($mps->pubs_q12), 0); ?></td>
                        <td><?php echo round(getTotal($mps->pubs_q1234), 0); ?></td>
                        <?php } ?>
  											<td><?php echo round(getTotal($mps->publications_5y), 1); ?></td>
  											<td><?php echo round(getTotal($mps->citations_5y), 1); ?></td>
  											<td><?php echo round(getTotal($mps->publications_3_years_before), 1); ?></td>
  											<td><?php echo round(getTotal($mps->citations_3_years_before), 1); ?></td>
  											<td><?php echo round(getTotal($mps->publications_2_years_before), 1); ?></td>
  											<td><?php echo round(getTotal($mps->citations_2_years_before), 1); ?></td>
  											<td><?php echo round(getTotal($mps->publications_previous_year), 1); ?></td>
  											<td><?php echo round(getTotal($mps->citations_previous_year), 1); ?></td>
  										</tr>
                      <?php } } } ?>
  									</tbody>
  								</table>
                </div><!-- /.tab-pane -->
              </div><!-- /.tab-content -->
            </div><!-- /.card-body -->
          </div><!-- /.card -->
        </div><!-- /.tab-pane -->
        <div class="tab-pane fade" id="<?php echo $tPrefix;?>-rankStats-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-rankStats-tabContent">
          <div class="card <?php echo $card_class; ?> card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
							  <li class="nav-item">
								<a class="nav-link active" id="<?php echo $tPrefix;?>-rankStats-meanScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-rankStats-meanScores-tabContent" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true"><?php echo _TABLE_STATS_M_AVG; ?></a>
							  </li>
							  <li class="nav-item">
								<a class="nav-link" id="<?php echo $tPrefix;?>-rankStats-q1Scores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-rankStats-q1Scores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q1; ?></a>
							  </li>
							  <li class="nav-item">
								<a class="nav-link" id="<?php echo $tPrefix;?>-rankStats-medianScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-rankStats-medianScores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q2; ?></a>
							  </li>
							  <li class="nav-item">
								<a class="nav-link" id="<?php echo $tPrefix;?>-rankStats-q3Scores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-rankStats-q3Scores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_Q3; ?></a>
							  </li>
								<li class="nav-item">
								<a class="nav-link" id="<?php echo $tPrefix;?>-rankStats-totalScores-tab" data-toggle="pill" href="#<?php echo $tPrefix;?>-rankStats-totalScores-tabContent" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false"><?php echo _TABLE_STATS_M_SUM; ?></a>
							  </li>
							</ul>
            </div><!-- /.card-header -->
            <div class="card-body">
            <div class="tab-content" id="custom-tabs-four-tabContent">
              <div class="tab-pane fade active show" id="<?php echo $tPrefix;?>-rankStats-meanScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-rankStats-meanScores-tabContent">
                <table id="tbl<?php echo $tPrefix;?>RankStatsSubUnitMeanScores" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_RANK; ?>/ <?php echo _TABLE_STATS_M_AVG; ?></th>
										</tr>
										<tr>
											<td colspan="2" class="mergedCell"></td>
											<th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
										</tr>
										<tr>
											<th scope="col"><?php echo _TABLE_STATS_TITLE_RANK; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                      <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                      <?php } ?>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($metricsPerRank as $mps){
											if ( !empty($mps->publications)) {
										?>
										<tr>
											<td><?php echo $mps->unit->name; ?></td>
											<td><?php echo sizeof($mps->publications); ?></td>
											<td><?php echo round(getMeanScore($mps->publications), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->citations), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->hindex), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->i10index), 0); ?></td>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <td><?php echo round(getMeanScore($mps->pubs_q12), 0); ?></td>
                      <td><?php echo round(getMeanScore($mps->pubs_q1234), 0); ?></td>
                      <?php } ?>
											<td><?php echo round(getMeanScore($mps->publications_5y), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->citations_5y), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->hindex_5y), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->i10index_5y), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->publications_3_years_before), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->citations_3_years_before), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->publications_2_years_before), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->citations_2_years_before), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->publications_previous_year), 0); ?></td>
											<td><?php echo round(getMeanScore($mps->citations_previous_year), 0); ?></td>
										</tr>
										<?php }	} ?>
									</tbody>
								</table>
              </div><!-- /.tab-pane -->
              <div class="tab-pane fade" id="<?php echo $tPrefix;?>-rankStats-medianScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-rankStats-medianScores-tabContent">
                <table id="tbl<?php echo $tPrefix;?>RankStatsSubUnitMedianScores" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_RANK; ?>/ <?php echo _TABLE_STATS_M_Q2; ?></th>
                    </tr>
                    <tr>
                      <td colspan="2" class="mergedCell"></td>
                      <th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
                      <th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
                      <th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
                      <th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
                      <th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
                    </tr>
                    <tr>
                      <th scope="col"><?php echo _TABLE_STATS_TITLE_RANK; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                      <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                      <?php } ?>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                      <th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($metricsPerRank as $mps){
                      if ( !empty($mps->publications)) {
                    ?>
                    <tr>
                      <td><?php echo $mps->unit->name; ?></td>
                      <td><?php echo sizeof($mps->publications); ?></td>
                      <td><?php echo round(getMedian($mps->publications), 1); ?></td>
                      <td><?php echo round(getMedian($mps->citations), 1); ?></td>
                      <td><?php echo round(getMedian($mps->hindex), 1); ?></td>
                      <td><?php echo round(getMedian($mps->i10index), 1); ?></td>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <td><?php echo round(getMedian($mps->pubs_q12), 0); ?></td>
                      <td><?php echo round(getMedian($mps->pubs_q1234), 0); ?></td>
                      <?php } ?>
                      <td><?php echo round(getMedian($mps->publications_5y), 1); ?></td>
                      <td><?php echo round(getMedian($mps->citations_5y), 1); ?></td>
                      <td><?php echo round(getMedian($mps->hindex_5y), 1); ?></td>
                      <td><?php echo round(getMedian($mps->i10index_5y), 1); ?></td>
                      <td><?php echo round(getMedian($mps->publications_3_years_before), 1); ?></td>
                      <td><?php echo round(getMedian($mps->citations_3_years_before), 1); ?></td>
                      <td><?php echo round(getMedian($mps->publications_2_years_before), 1); ?></td>
                      <td><?php echo round(getMedian($mps->citations_2_years_before), 1); ?></td>
                      <td><?php echo round(getMedian($mps->publications_previous_year), 1); ?></td>
                      <td><?php echo round(getMedian($mps->citations_previous_year), 1); ?></td>
                    </tr>
                    <?php } } ?>
                  </tbody>
                </table>
              </div><!-- /.tab-pane -->
              <div class="tab-pane fade" id="<?php echo $tPrefix;?>-rankStats-q1Scores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-rankStats-q1Scores-tabContent">
                <table id="tbl<?php echo $tPrefix;?>RankStatsSubUnitQ1Scores" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_RANK; ?>/ <?php echo _TABLE_STATS_M_Q1; ?></th>
										</tr>
										<tr>
											<td colspan="2" class="mergedCell"></td>
											<th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
										</tr>
										<tr>
											<th scope="col"><?php echo _TABLE_STATS_TITLE_RANK; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                      <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                      <?php } ?>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($metricsPerRank as $mps){
											if ( !empty($mps->publications)) {
										?>
										<tr>
											<td><?php echo $mps->unit->name; ?></td>
											<td><?php echo sizeof($mps->publications); ?></td>
											<td><?php echo round(getQ1Score($mps->publications), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->citations), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->hindex), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->i10index), 1); ?></td>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <td><?php echo round(getQ1Score($mps->pubs_q12), 0); ?></td>
                      <td><?php echo round(getQ1Score($mps->pubs_q1234), 0); ?></td>
                      <?php } ?>
											<td><?php echo round(getQ1Score($mps->publications_5y), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->citations_5y), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->hindex_5y), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->i10index_5y), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->publications_3_years_before), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->citations_3_years_before), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->publications_2_years_before), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->citations_2_years_before), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->publications_previous_year), 1); ?></td>
											<td><?php echo round(getQ1Score($mps->citations_previous_year), 1); ?></td>
										</tr>
										<?php } }	?>
									</tbody>
								</table>
              </div><!-- /.tab-pane -->
              <div class="tab-pane fade" id="<?php echo $tPrefix;?>-rankStats-q3Scores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-rankStats-q3Scores-tabContent">
                <table id="tbl<?php echo $tPrefix;?>RankStatsSubUnitQ3Scores" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th colspan="<?php echo $unit_sub_cols; ?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_RANK; ?>/ <?php echo _TABLE_STATS_M_Q3; ?></th>
										</tr>
										<tr>
											<td colspan="2" class="mergedCell"></td>
											<th colspan="<?php echo intval($unit_sub_cols - 12);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
											<th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
										</tr>
										<tr>
											<th scope="col"><?php echo _TABLE_STATS_TITLE_RANK; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                      <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                      <?php } ?>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <th scope="col"><?php echo _LABEL_HINDEX; ?></th>
                      <th scope="col"><?php echo _LABEL_I10INDEX; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($metricsPerRank as $mps){
											if ( !empty($mps->publications)) {
										?>
										<tr>
											<td><?php echo $mps->unit->name; ?></td>
											<td><?php echo sizeof($mps->publications); ?></td>
											<td><?php echo round(getQ3Score($mps->publications), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->citations), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->hindex), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->i10index), 1); ?></td>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <td><?php echo round(getQ3Score($mps->pubs_q12), 0); ?></td>
                      <td><?php echo round(getQ3Score($mps->pubs_q1234), 0); ?></td>
                      <?php } ?>
											<td><?php echo round(getQ3Score($mps->publications_5y), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->citations_5y), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->hindex_5y), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->i10index_5y), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->publications_3_years_before), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->citations_3_years_before), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->publications_2_years_before), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->citations_2_years_before), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->publications_previous_year), 1); ?></td>
											<td><?php echo round(getQ3Score($mps->citations_previous_year), 1); ?></td>
										</tr>
                   <?php } } ?>
									</tbody>
								</table>
              </div><!-- /.tab-pane -->
              <div class="tab-pane fade" id="<?php echo $tPrefix;?>-rankStats-totalScores-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-rankStats-totalScores-tabContent">
                <table id="tbl<?php echo $tPrefix;?>RankStatsSubUnitTotalScores" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th colspan="<?php echo intval($unit_sub_cols - 4);?>" class="mergedCell"><?php echo $unit->name;?> / <?php echo _TABLE_STATS_INFO_PER_RANK; ?>/ <?php echo _TABLE_STATS_M_SUM; ?></th>
										</tr>
										<tr>
											<td colspan="2" class="mergedCell"></td>
											<th colspan="<?php echo intval($unit_sub_cols - 14);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
											<th colspan="2" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
											<th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
										</tr>
										<tr>
											<th scope="col"><?php echo _TABLE_STATS_TITLE_RANK; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_NUMBER_OF_FACULTY_MEMBERS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <th scope="col"><?php echo _LABEL_Q1_Q2; ?></th>
                      <th scope="col"><?php echo _LABEL_JOURNALS; ?></th>
                      <?php } ?>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
											<th scope="col"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($metricsPerRank as $mps){
											if ( !empty($mps->publications)) {
										?>
										<tr>
											<td><?php echo $mps->unit->name; ?></td>
											<td><?php echo sizeof($mps->publications); ?></td>
											<td><?php echo round(getTotal($mps->publications), 1); ?></td>
											<td><?php echo round(getTotal($mps->citations), 1); ?></td>
                      <?php if ($tbl_provider_id == "scopus") { ?>
                      <td><?php echo round(getTotal($mps->pubs_q12), 0); ?></td>
                      <td><?php echo round(getTotal($mps->pubs_q1234), 0); ?></td>
                      <?php } ?>
											<td><?php echo round(getTotal($mps->publications_5y), 1); ?></td>
											<td><?php echo round(getTotal($mps->citations_5y), 1); ?></td>
											<td><?php echo round(getTotal($mps->publications_3_years_before), 1); ?></td>
											<td><?php echo round(getTotal($mps->citations_3_years_before), 1); ?></td>
											<td><?php echo round(getTotal($mps->publications_2_years_before), 1); ?></td>
											<td><?php echo round(getTotal($mps->citations_2_years_before), 1); ?></td>
											<td><?php echo round(getTotal($mps->publications_previous_year), 1); ?></td>
											<td><?php echo round(getTotal($mps->citations_previous_year), 1); ?></td>
										</tr>
                    <?php } }	?>
									</tbody>
								</table>
              </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
			</div><!-- /.card-body -->
          </div><!-- /.card -->
        </div><!-- /.tab-pane -->
        <div class="tab-pane fade" id="<?php echo $tPrefix;?>-facultyMemberStats-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-facultyMemberStats-tabContent">
          <table id="tbl<?php echo $tPrefix;?>FacultyMembers" class="table table-bordered table-hover">
            <thead>
              <tr>
                <td colspan="3" class="mergedCell"></td>
                <th colspan="<?php echo intval($fm_cols-14);?>" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_ALL; ?></th>
                <th colspan="4" class="mergedCell"><?php echo _TABLE_STATS_METRIC_YEARS_5; ?></th>
                <th colspan="2" class="mergedCell"><?php echo $report_3_years_before; ?></th>
                <th colspan="2" class="mergedCell"><?php echo $report_2_years_before; ?></th>
                <th colspan="2" class="mergedCell"><?php echo $report_previous_year; ?></th>
                <td colspan="1" class="mergedCell"></td>
              </tr>
              <tr>
                <th class="th-sm">Μέλος ΔΕΠ/ΕΔΙΠ</th>
                <th class="th-sm"><?php echo _TABLE_STATS_TITLE_RANK; ?></th>
                <th class="th-sm">ΕΚΔ</th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                <th class="th-sm">h</th>
                <th class="th-sm">i10</th>
                <th class="th-sm">m</th>
                <?php if ($tbl_provider_id == "scopus") { ?>
                <th class="th-sm"><?php echo _LABEL_Q1_Q2; ?></th>
                <th class="th-sm"><?php echo _LABEL_JOURNALS; ?></th>
                <?php } ?>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                <th class="th-sm">h</th>
                <th class="th-sm">i10</th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?></th>
                <th class="th-sm"><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?></th>
                <th class="th-sm">ΠΑΑ</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($_SESSION["role"] == "admin"){
                foreach ($fmMetrics as $fm){
					$fm_info_metadata = null;
					$fm_metrics_metadata = null;
					$fm_info_metadata = json_decode($fm->info_metadata);
					if(!is_null($fm->metrics_metadata)) {
						$fm_metrics_metadata = json_decode($fm->metrics_metadata);
					}
                  echo "<tr>";
                  echo "<td><a href='member.php?fmid=".$fm_info_metadata->id."&rid=".$report_id."'>".$fm_info_metadata->last_name.", ".$fm_info_metadata->first_name."</a></td>";
                  echo "<td>".$fm_info_metadata->rank->full_title."</td>";
                  echo "<td>".$fm_info_metadata->phd_year."</td>";

                  /* Total number of publications */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                    echo "<td>".$fm_metrics_metadata->metrics_total->publications."</td>";
                  else echo "<td></td>";

                  /* Total number of citations */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                    echo "<td>".$fm_metrics_metadata->metrics_total->citations."</td>";
                  else echo "<td></td>";

                  /* Total h index */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                    echo "<td>".$fm_metrics_metadata->metrics_total->hindex."</td>";
                  else echo "<td></td>";

                  /* Total i10 index */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                    echo "<td>".$fm_metrics_metadata->metrics_total->i10index."</td>";
                  else echo "<td></td>";

                  /* m index */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_misc) )
                    echo "<td>".$fm_metrics_metadata->metrics_misc->mindex."</td>";
                  else echo "<td></td>";

                  if ($tbl_provider_id == "scopus"){
                    if ( !empty($fm->author_profile->pubsQ1Q2) )
                      echo "<td>".$fm->author_profile->pubsQ1Q2."</td>";
                    else echo "<td></td>";

                    if ( !empty($fm->author_profile->pubsJournals) )
                      echo "<td>".$fm->author_profile->pubsJournals."</td>";
                      else echo "<td></td>";
                  }

                  /* Last 5 years - number of publications */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                    echo "<td>".$fm_metrics_metadata->metrics_5y->publications."</td>";
                  else echo "<td></td>";

                  /* Last 5 years - number of citations */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                    echo "<td>".$fm_metrics_metadata->metrics_5y->citations."</td>";
                  else echo "<td></td>";

                  /* Last 5 years - h index */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                    echo "<td>".$fm_metrics_metadata->metrics_5y->hindex."</td>";
                  else echo "<td></td>";

                  /* Last 5 years - i10 index */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                    echo "<td>".$fm_metrics_metadata->metrics_5y->i10index."</td>";
                  else echo "<td></td>";

                  /* current year metrics */
                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->publications_per_year) ){
                    $value = getElementFromKey($fm_metrics_metadata->publications_per_year, $report_3_years_before);
                    echo "<td>$value</td>";
                  }
                  else echo "<td></td>";

                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->citations_per_year) ){
                    $value = getElementFromKey($fm_metrics_metadata->citations_per_year, $report_3_years_before);
                    echo "<td>$value</td>";
                  }
                  else echo "<td></td>";

                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->publications_per_year) ){
                    $value = getElementFromKey($fm_metrics_metadata->publications_per_year, $report_2_years_before);
                    echo "<td>$value</td>";
                  }
                  else echo "<td></td>";

                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->citations_per_year) ){
                    $value = getElementFromKey($fm_metrics_metadata->citations_per_year, $report_2_years_before);
                    echo "<td>$value</td>";
                  }
                  else echo "<td></td>";

                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->publications_per_year) ){
                    $value = getElementFromKey($fm_metrics_metadata->publications_per_year, $report_previous_year);
                    echo "<td>$value</td>";
                  }
                  else echo "<td></td>";

                  if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->citations_per_year) ){
                    $value = getElementFromKey($fm_metrics_metadata->citations_per_year, $report_previous_year);
                    echo "<td>$value</td>";
                  }
                  else echo "<td></td>";


                  if ( !is_null($fm->metrics_metadata) && isset ($fm_metrics_metadata->metrics_misc) )
                    echo "<td>".$fm_metrics_metadata->metrics_misc->most_paper_citations."</td>";
                    else echo "<td></td>";
                  echo "</tr>";
                } // end of for loop

              } // end of if loop
              else if ($_SESSION["role"] == "fm"){
                foreach ($fmMetrics as $fm){
                  if (json_decode($fm->info_metadata)->id == $_SESSION["member_id"]){
					$fm_info_metadata = json_decode($fm->info_metadata);
					if(!is_null($fm->metrics_metadata)) {
						$fm_metrics_metadata = json_decode($fm->metrics_metadata);
					}
                    echo "<tr>";
                    echo "<td><a href='member.php?fmid=".$fm_info_metadata->id."&rid=".$report_id."'>".$fm_info_metadata->last_name.", ".$fm_info_metadata->first_name."</a></td>";
                    echo "<td>".$fm_info_metadata->rank->full_title."</td>";
                    echo "<td>".$fm_info_metadata->phd_year."</td>";

                    /* Total number of publications */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                      echo "<td>".$fm_metrics_metadata->metrics_total->publications."</td>";
                    else echo "<td></td>";

                    /* Total number of citations */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                      echo "<td>".$fm_metrics_metadata->metrics_total->citations."</td>";
                    else echo "<td></td>";

                    /* Total h index */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                      echo "<td>".$fm_metrics_metadata->metrics_total->hindex."</td>";
                    else echo "<td></td>";

                    /* Total i10 index */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_total) )
                      echo "<td>".$fm_metrics_metadata->metrics_total->i10index."</td>";
                    else echo "<td></td>";

                    /* m index */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_misc) )
                      echo "<td>".$fm_metrics_metadata->metrics_misc->mindex."</td>";
                    else echo "<td></td>";

                    /* Last 5 years - number of publications */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                      echo "<td>".$fm_metrics_metadata->metrics_5y->publications."</td>";
                    else echo "<td></td>";

                    /* Last 5 years - number of citations */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                      echo "<td>".$fm_metrics_metadata->metrics_5y->citations."</td>";
                    else echo "<td></td>";

                    /* Last 5 years - h index */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                      echo "<td>".$fm_metrics_metadata->metrics_5y->hindex."</td>";
                    else echo "<td></td>";

                    /* Last 5 years - i10 index */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_5y) )
                      echo "<td>".$fm_metrics_metadata->metrics_5y->i10index."</td>";
                    else echo "<td></td>";

                    /* current year metrics */
                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->publications_per_year) ){
                      $value = getElementFromKey($fm_metrics_metadata->publications_per_year, $report_3_years_before);
                      echo "<td>$value</td>";
                    }
                    else echo "<td></td>";

                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->citations_per_year) ){
                      $value = getElementFromKey($fm_metrics_metadata->citations_per_year, $report_3_years_before);
                      echo "<td>$value</td>";
                    }
                    else echo "<td></td>";

                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->publications_per_year) ){
                      $value = getElementFromKey($fm_metrics_metadata->publications_per_year, $report_2_years_before);
                      echo "<td>$value</td>";
                    }
                    else echo "<td></td>";

                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->citations_per_year) ){
                      $value = getElementFromKey($fm_metrics_metadata->citations_per_year, $report_2_years_before);
                      echo "<td>$value</td>";
                    }
                    else echo "<td></td>";

                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->publications_per_year) ){
                      $value = getElementFromKey($fm_metrics_metadata->publications_per_year, $report_previous_year);
                      echo "<td>$value</td>";
                    }
                    else echo "<td></td>";

                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->citations_per_year) ){
                      $value = getElementFromKey($fm_metrics_metadata->citations_per_year, $report_previous_year);
                      echo "<td>$value</td>";
                    }
                    else echo "<td></td>";


                    if ( !is_null($fm->metrics_metadata) && isset($fm_metrics_metadata->metrics_misc) )
                      echo "<td>".$fm_metrics_metadata->metrics_misc->most_paper_citations."</td>";
                      else echo "<td></td>";
                    echo "</tr>";

					break;
                  } // end of if loop
                } // end of for loop
              }
              ?>

            </tbody>
          </table>
          <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-facultymember-column-info">Επεξήγηση στηλών</button>
        </div><!-- /.tab-pane -->
        <div class="tab-pane fade" id="<?php echo $tPrefix;?>-top210-tabContent" role="tabpanel" aria-labelledby="<?php echo $tPrefix;?>-top210-tabContent">
          <div class="col-md-12">
            Αριθμός αναφορών: <?php echo $metrics_210; ?>
          </div>
        </div><!-- /.tab-pane -->
      </div><!-- /.tab-content -->
    </div><!-- /.card-body -->
  </div><!-- /.card -->
</div>
