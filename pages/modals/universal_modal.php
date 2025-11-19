<?php
    function generateUniversalModal() {
        $output = '
            <div id="universalModal" class="modal-backdrop modal-hidden">
                <div id="universalModalInner" class="modal-content">
                    <div class="modal-header">
                        <h3 id="universalModalTitle" class="modal-title"></h3>
                        <button class="close-btn" onclick="closeModal(\'universalModal\')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
                        </button>
                    </div>
                    
                    <div id="modalContentArea" class="modal-body-container"></div>
                </div>
            </div>
        ';
        echo $output;
    }
?>