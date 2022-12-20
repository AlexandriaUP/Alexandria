<?php
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("../uppa_core/functions.php");
require_once("../uppa_core/settings/components.php");
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['tutorial']; ?></title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="../dist/css/adminlte.min.css">
      <!-- Alexandria style -->
      <link rel="stylesheet" href="../dist/css/alexandria.css">
      <!-- Google Font: Source Sans Pro -->
      <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
      <?php echo getFavicon(); ?>
   </head>
   <body class="hold-transition sidebar-mini">
      <div class="wrapper">
         <!-- Top navigation bar -->
         <?php echo getTopNavBar($_SESSION['lang']); ?>
         <!-- Side bar -->
         <?php echo getSideBarMenu($_SESSION["role"], "tutorial"); ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo _TUTORIAL_HEADER; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME; ?></a></li>
                           <li class="breadcrumb-item"><?php echo _TUTORIAL_HEADER; ?></li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-md-12">
                        <p>
                           Στην "Αλεξάνδρεια", οι χρήστες μπορούν να συνδεθούν είτε ως <a href="#hVisitor">επισκέπτες</a> είτε ως <a href="#hUPATmember">μέλη ΔΕΠ/ΕΔΙΠ</a> του Πανεπιστημίου Πατρών.
                        </p>
                        <h4 id="hVisitor">Σύνδεση ως επισκέπτης</h4>
                        <div class="row">
                           <div class="col-md-6">
                              <p>Η πρώτη οθόνη εμφανίζει τις διαθέσιμες αναφορές. Επιλέγοντας το "Δείτε λεπτομέρειες" (1), οι χρήστες μπορούν να δουν περισσότερες λεπτομέρειες σχετικά με την αναφορά.</p>
                           </div>
                           <div class="col-md-6"><img alt ="screenshot 1 (reports)" src="../dist/img/tutorial/tutorial_alexandria_reports.jpg" width="100%"></img></div>
                        </div>
                        <br>
                        <div class="row">
                           <div class="col-md-6">
                              <img alt ="screenshot 2 (report)" src="../dist/img/tutorial/tutorial_alexandria_report.jpg" width="100%"></img>
                           </div>
                           <div class="col-md-6">
                              <p>Για την κάθε αναφορά, οι χρήστες μπορούν να δουν λεπτομέρειες σχετικά με τα βιβλιομετρικά στοιχεία όπως αυτά καταγράφονται στις βάσεις Google Scholar (2) και Elsevier Scopus (3).
                                 Συγκεκριμένα, μπορούν να δουν (4):
                              <ul>
                                 <li>τον μέσο όρο</li>
                                 <li>τους δείκτες Q1, Q2 (διάμεση τιμή), Q3</li>
                                 <li>και το άθροισμα</li>
                              </ul>
                              για τα εξής (5):
                              <ul>
                                 <li> τον αριθμό των δημοσιεύσεων,</li>
                                 <li>τον αριθμό των αναφορών, </li>
                                 <li>τους δείκτες h-index και i-10 index</li>
                              </ul>
                              για όλα τα έτη, για τα τελευταία 5 χρόνια, και για τα τρία τελευταία έτη (π.χ., 2019, 2020, 2021).
                              Η ανάλυση των παραπάνω δεικτών γίνεται (6):
                              <ul>
                                 <li>συγκεντρωτικά για το Πανεπιστήμιο Πατρών,</li>
                                 <li> για την κάθε σχολή,</li>
                                 <li> για το κάθε τμήμα (<i>η επιλογή του τμήματος γίνεται αφού πρώτα ο χρήστης επιλέξει την αντίστοιχη σχολή</i>),</li>
                                 <li> και για την κάθε βαθμίδα.</li>
                              </ul>
                              Επίσης, εμφανίζεται το σύνολο των αναφορών για τα κορυφαία 210 προφίλ για το Πανεπιστήμιο Πατρών (δείκτης που χρησιμοποιείται για την κατάταξη των πανεπιστημίων στο Webometrics).
                              </p>
                           </div>
                        </div>
                        <hr>
                        <h4 id="hUPATmember">Σύνδεση ως μέλος ΔΕΠ/ΕΔΙΠ</h4>
                        <div class="row">
                           <div class="col-md-6">
                              <p>Το μέλος ΔΕΠ/ΕΔΙΠ έχει πρόσβαση στην ίδια πληροφορία με τους επισκέπτες, όπως περιγράφεται παραπάνω.
                                 Ωστόσο, στην όψη της κάθε αναφοράς, μπορεί πέρα από τα συγκεντρωτικά στοιχεία, να δει και βιβλιομετρικά δεδομένα που αφορούν τον ίδιο/την ίδια.
                                 Αυτό γίνεται μέσω της καρτέλας "Τα στοιχεία μου" (7). Πατώντας πάνω στο όνομά του/της, το μέλος ΔΕΠ/ΕΔΙΠ μπορεί να δει περισσότερα στοιχεία (8).
                              </p>
                           </div>
                           <div class="col-md-6">
                              <img alt ="screenshot 3 (report faculty member)" src="../dist/img/tutorial/tutorial_alexandria_report_dep.jpg" width="100%"></img>
                           </div>
                        </div>
                        <br>
                        <div class="row">
                           <div class="col-md-6">
                              <img alt ="screenshot 4 (report faculty member more info)" src="../dist/img/tutorial/tutorial_alexandria_report_dep_more_info.jpg" width="100%"></img>
                           </div>
                           <div class="col-md-6">
                              <p>Συγκεκριμένα, το μέλος ΔΕΠ/ΕΔΙΠ μπορεί να δει τα στοιχεία του (ονοματεπώνυμο, βαθμίδα, και τμήμα) τη στιγμή δημιουργίας της αναφοράς (9).
                                 Επίσης, μπορεί να δει συγκεντρωτικά τις βασικές μετρικές (10):
                              <ul>
                                 <li>αριθμό δημοσιεύσεων,</li>
                                 <li>αριθμό αναφορών,</li>
                                 <li>δείκτη h-index,</li>
                                 <li>δείκτη i10</li>
                              </ul>
                              όπως αποτυπώνονται στις βάσεις των Google Scholar και Elsevier Scopus.
                              Επίσης, μπορεί να δει το σύνολο το άρθρων που έχει δημοσιεύσει σε περιοδικά Q1, Q2, Q3, και Q4, σύμφωνα με την κατάταξη <a href="https://www.scimagojr.com" target="_blank">Scimago</a> (11).
                              Τέλος, το μέλος ΔΕΠ/ΕΔΙΠ μπορεί να δει αναλυτικά τα άρθρα που έχει δημοσιεύσει, όπως αυτά έχουν καταγραφεί στις βάσεις των Google Scholar (12) και Elsevier Scopus (13).
                              </p>
                           </div>
                        </div>
                        <br>
                        <div class="row">
                           <div class="col-md-6">
                              <p>
                                 Μια επιπλέον λειτουργία που παρέχεται στο μέλος ΔΕΠ/ΕΔΙΠ είναι η διαχείριση του προφίλ του (14). Το μέλος ΔΕΠ/ΕΔΙΠ μπορεί να τροποποιήσει τα εξής στοιχεία:
                              <ul>
                                 <li>ονοματεπώνυμο,</li>
                                 <li>έτος κτήσης διδακτορικού διπλώματος,</li>
                                 <li>βαθμίδα,</li>
                                 <li>τμήμα,</li>
                                 <li>αναγνωριστικό για το Google Scholar προφίλ του μέλους ΔΕΠ/ΕΔΙΠ (15),</li>
                                 <li>αναγνωριστικό για το Scopus προφίλ του μέλους ΔΕΠ/ΕΔΙΠ (16)</li>
                              </ul>
                              Επίσης, μπορεί να δηλώσει ότι τα στοιχεία που αναγράφονται είναι σωστά (17) και να ενημερώσει το προφίλ του, πατώντας το αντίστοιχο κουμπί (18).
                              </p>
                           </div>
                           <div class="col-md-6">
                              <img alt ="screenshot 5 (report faculty member profile)" src="../dist/img/tutorial/tutorial_alexandria_profile.jpg" width="100%"></img>
                           </div>
                        </div>
                        <!-- /.card -->
                     </div>
                  </div>
                  <!-- /.row -->
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
            <!-- Back to top -->
            <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
            <i class="fas fa-chevron-up"></i>
            </a>
         </div>
         <?php echo getFooter(); ?>
      </div>
      <!-- ./wrapper -->
      <!-- jQuery -->
      <script src="../plugins/jquery/jquery.min.js"></script>
      <!-- Bootstrap 4 -->
      <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- AdminLTE App -->
      <script src="../dist/js/adminlte.min.js"></script>
   </body>
</html>
