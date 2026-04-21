<div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-download" style="color: #10b981;"></i> Exporter les données</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="row g-3">
                    <div class="col-12 mb-3">
                        <div class="alert alert-success" style="background: #ecfdf5; border: none; border-radius: 16px;">
                            <i class="fas fa-chart-line me-2"></i> Choisissez le format d'export
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn-export-option w-100" id="exportExcel" style="background: none; border: none;">
                            <div class="card border-0 shadow-sm rounded-3 p-3 text-center export-card" style="transition: all 0.2s;">
                                <i class="fas fa-file-excel fa-3x" style="color: #10b981;"></i>
                                <h6 class="mt-2 mb-0">Excel</h6><small class="text-muted">.xlsx</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn-export-option w-100" id="exportPDF" style="background: none; border: none;">
                            <div class="card border-0 shadow-sm rounded-3 p-3 text-center export-card" style="transition: all 0.2s;">
                                <i class="fas fa-file-pdf fa-3x" style="color: #ef4444;"></i>
                                <h6 class="mt-2 mb-0">PDF</h6><small class="text-muted">.pdf</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn-export-option w-100" id="exportCSV" style="background: none; border: none;">
                            <div class="card border-0 shadow-sm rounded-3 p-3 text-center export-card" style="transition: all 0.2s;">
                                <i class="fas fa-file-csv fa-3x" style="color: #3b82f6;"></i>
                                <h6 class="mt-2 mb-0">CSV</h6><small class="text-muted">.csv</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Fermer</button>
            </div>
        </div>
    </div>
</div>