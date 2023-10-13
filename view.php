<?php
    class View {

        private $headerImg = "./assets/img/ForceWanHeader.png";
        private $banImg = "./assets/img/ForceWanBeWithU.png";

        public function displayTopPage() {
            $html = '<header class="bg-dark text-white text-center py-4">
                        <img src="'. $this->headerImg .'" class="img-fluid" alt="Image">
                    </header>
                    <nav class="navbar navbar-expand-lg bg-dark-subtle">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">Navbar</a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="#">Home</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" href="#">Features</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" href="#">Pricing</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                                </li>
                            </ul>
                            </div>
                        </div>
                    </nav>
                    <div class="container mt-4">
                        <img src="'. $this->banImg .'" class="img-fluid" alt="Image">
                    </div>';
            echo $html;
        }

        public function displayRestPage($totalS, $totalP, $data, $isOnLive, $isParticipate_A, $isParticipate_B, $isParticipate_C, $isParticipate_D, $participantsOnline) {

            $html = '<div class="container mt-4">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 mt-4">
                                '. $this->displayButtons($isOnLive, $isParticipate_A, $isParticipate_B, $isParticipate_C, $isParticipate_D, $participantsOnline) .'
                            </div>
                            <div class="col-lg-6 col-md-12">
                                '. $this->displayTable($data) .'
                                '. $this->displayTotals($totalS, $totalP) .'
                            </div>
                        </div>
                        '. $this->displayGraphs() .'
                    </div>';
            echo $html;
        }

        public function displayDATA($data) {
            echo $data;
        }

        private function displayTable($data) {
            $table = '<div class="align-items-center d-md-flex flex-md-column">
                        <div class="flex-md-column">
                            <div class="table-responsive" style="max-height: 250px; margin-top: 1em;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Table</th>
                                            <th>ID</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
            foreach ($data as $row) {
                $table .= '<tr>
                            <td>' . $row['table'] . '</td>
                            <td>' . $row['id'] . '</td>
                            <td>' . date('Y-m-d H:i:s', strtotime($row['date'])) . '</td>
                          </tr>';
            }

            $table .= '             </tbody>
                                </table>
                            </div>
                        </div>
                    </div>';
            return $table;
        }

        private function displayButtons($isOnLive, $isParticipate_A, $isParticipate_B, $isParticipate_C, $isParticipate_D, $participantsOnline) {
            $html = '<div class="d-md-flex flex-md-row mt-4 align-items-center" style="gap: 2em;"> 
                        <div class="flex-md-column">
                            <div class="A">';
        
            if ($isOnLive) {
                $html .= '<a href="index.php?action=stopStream" class="btn btn-dark">Stop Stream</a></br>
                          <p class="mt-4 justify-content-center">Online : <strong style="color: green">'. $participantsOnline .'</strong></p>';
            } else {
                $html .= '<a href="index.php?action=startNewStream" class="btn btn-dark">Start New Stream</a>';
            }
            
            $html .= '      </div>
                        </div>
                        <div class="flex-md-column" style="gap: 1em;">
                            <div class="B">
                                <div style="margin-bottom: 1em;">
                                    <span>Participant: <strong>A</strong>&nbsp;</span>';
            if ($isOnLive) {
                if ($isParticipate_A) {
                    $html .= '<a href="index.php?action=stopParticipate&participant=A" class="btn btn-dark">Stop Participation</a>
                            <a href="index.php?action=sendLike&participant=A" class="btn btn-dark">Send Like</a>';
                } else {
                    $html .= '<a href="index.php?action=startParticipate&participant=A" class="btn btn-dark">Start Participation</a>';
                }
            }
            
            $html .= '          </div>
                                <div style="margin-bottom: 1em;">
                                    <span>Participant: <strong>B</strong>&nbsp;</span>';
        
            if ($isOnLive) {                            
                if ($isParticipate_B) {
                    $html .= '<a href="index.php?action=stopParticipate&participant=B" class="btn btn-dark">Stop Participation</a>
                            <a href="index.php?action=sendLike&participant=B" class="btn btn-dark">Send Like</a>';
                } else {
                    $html .= '<a href="index.php?action=startParticipate&participant=B" class="btn btn-dark">Start Participation</a>';
                }
            }

            $html .= '          </div>
                                <div style="margin-bottom: 1em;">
                                    <span>Participant: <strong>C</strong>&nbsp;</span>';
            
            if ($isOnLive) {                        
                if ($isParticipate_C) {
                    $html .= '<a href="index.php?action=stopParticipate&participant=C" class="btn btn-dark">Stop Participation</a>
                            <a href="index.php?action=sendLike&participant=C" class="btn btn-dark">Send Like</a>';
                } else {
                    $html .= '<a href="index.php?action=startParticipate&participant=C" class="btn btn-dark">Start Participation</a>';
                }
            }
            
            $html .= '          </div>
                                <div style="margin-bottom: 1em;">
                                    <span>Participant: <strong>D</strong>&nbsp;</span>';
            
            if ($isOnLive) {
                if ($isParticipate_D) {
                    $html .= '<a href="index.php?action=stopParticipate&participant=D" class="btn btn-dark">Stop Participation</a>
                            <a href="index.php?action=sendLike&participant=D" class="btn btn-dark">Send Like</a>';
                } else {
                    $html .= '<a href="index.php?action=startParticipate&participant=D" class="btn btn-dark">Start Participation</a>';
                }
            }
        
            $html .= '          </div>
                            </div>
                        </div>
                    </div>';
            return $html;
        }
        
        private function displayTotals($totalS, $totalP) {
            return '
                <div class="justify-content-center d-md-flex flex-md-row">
                    <span> total Streams: <strong>'. $totalS .'</strong>&nbsp; &nbsp;</span></br>
                    <span> total Potential Participants: <strong>'. $totalP .'</strong></span>
                </div>';
        }

        private function displayGraphs() {
            return '
                <div class="d-md-flex flex-md-row">
                    <div class="d-md-flex flex-lg-row">
                        <div id="chart1" style="width:350px; height:400px;" class="container mt-4"></div>
                        <div id="chart2" style="width:350px; height:400px;" class="container mt-4"></div>
                        <div id="chart3" style="width:500px; height:400px;" class="container mt-4"></div>
                    </div>
                </div>';
        }
    }
?>