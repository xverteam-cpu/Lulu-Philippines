<style>
  * {
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  body {
    background-color: #F8FAFC;
  }

  .admin-shell {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 32px;
    min-height: 100vh;
    background-color: #F8FAFC;
  }

  /* Top Navigation Bar */
  .admin-nav {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 16px;
    padding: 0;
    background-color: transparent;
    border: 0;
    border-radius: 0;
    position: sticky;
    top: 0;
    z-index: 100;
    flex-wrap: wrap;
  }

  .admin-nav-links {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
  }

  .admin-nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 44px;
    padding: 0 18px;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    background-color: #ffffff;
    color: #475569;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all .18s ease;
    white-space: nowrap;
  }

  .admin-nav-btn:hover {
    background-color: #F8FAFC;
    border-color: #E5E7EB;
    color: #0F172A;
  }

  .admin-nav-btn.active {
    background-color: #166534;
    border-color: #166534;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(22,101,52,.20);
  }

  /* Header Section */
  .admin-header {
    padding: 48px 32px 40px;
    background-color: #ffffff;
    border: 1px solid #E5E7EB;
    border-radius: 18px;
    position: sticky;
    top: 88px;
    z-index: 99;
    box-shadow: 0 1px 2px rgba(15,23,42,.05), 0 8px 30px rgba(15,23,42,.06);
    margin-top: 20px;
  }

  .admin-header-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
  }

  .admin-header-info {
    flex: 1;
  }

  .admin-title {
    margin: 0;
    font-size: 36px;
    font-weight: 700;
    color: #0F172A;
    letter-spacing: -0.5px;
  }

  .admin-copy {
    margin: 12px 0 0;
    font-size: 15px;
    line-height: 1.6;
    color: #475569;
    font-weight: 500;
  }

  .admin-header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .header-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 44px;
    padding: 0 22px;
    border: none;
    border-radius: 12px;
    background-color: #166534;
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    white-space: nowrap;
    text-decoration: none;
  }

  .header-btn:hover {
    transform: translateY(-1px);
    background-color: #0f3d2e;
    box-shadow: 0 4px 16px rgba(22,101,52,.18);
  }

  .header-btn:active {
    transform: translateY(0) scale(0.98);
  }

  .header-btn-secondary {
    background-color: #ffffff;
    color: #0F172A;
    border: 1px solid #D1D5DB;
  }

  .header-btn-secondary:hover {
    background-color: #F8FAFC;
  }

  .admin-backup-btn {
    min-width: 120px;
  }

  /* Content Wrapper */
  .admin-content {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Statistics Grid */
  .summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .summary-card {
    background-color: #ffffff;
    border: 1px solid #E5E7EB;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 1px 2px rgba(15,23,42,.05), 0 8px 30px rgba(15,23,42,.06);
    transition: transform .18s ease, box-shadow .18s ease;
    position: relative;
    overflow: hidden;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15,23,42,.08);
    border-color: #D1D5DB;
  }

  .summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #14532d, #166534);
  }

  .summary-card-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: #fef2f2;
    color: #14532d;
    font-size: 20px;
    margin-bottom: 12px;
  }

  .summary-value {
    display: block;
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }

  .summary-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Users Panel */
  .users-panel {
    background-color: #ffffff;
    border: 1px solid #E5E7EB;
    border-radius: 18px;
    box-shadow: 0 1px 2px rgba(15,23,42,.05), 0 8px 30px rgba(15,23,42,.06);
    overflow: hidden;
  }

  .users-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 28px 32px;
    border-bottom: 1px solid #E5E7EB;
    flex-wrap: wrap;
    gap: 16px;
  }

  .users-panel-title {
    flex: 1;
    min-width: 220px;
  }

  .users-panel-title-main {
    display: block;
    font-size: 22px;
    font-weight: 600;
    color: #0F172A;
    margin-bottom: 6px;
  }

  .users-panel-title-sub {
    display: block;
    font-size: 14px;
    color: #94A3B8;
    font-weight: 400;
  }

  .search-box {
    position: relative;
    flex: 1;
    min-width: 280px;
  }

  .search-box form {
    display: flex;
    gap: 8px;
  }

  .search-input {
    flex: 1;
    height: 46px;
    padding: 0 16px;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    font-size: 15px;
    color: #0F172A;
    background-color: #F8FAFC;
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
  }

  .search-input::placeholder {
    color: #94A3B8;
  }

  .search-input:focus {
    outline: none;
    background-color: #ffffff;
    border-color: #166534;
    box-shadow: 0 0 0 4px rgba(22,101,52,.08);
  }

  /* Table Styles */
  .table-wrap {
    overflow-x: auto;
  }

  .users-table {
    width: 100%;
    border-collapse: collapse;
  }

  .users-table thead {
    background-color: #F8FAFC;
    border-bottom: 1px solid #E5E7EB;
  }

  .users-table th {
    padding: 16px 18px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }

  .users-table td {
    padding: 18px 18px;
    border-bottom: 1px solid #EEF2F7;
    font-size: 15px;
    color: #475569;
    vertical-align: middle;
  }

  .user-cell-ip {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 13px;
    color: #64748B;
  }

  .users-table tbody tr {
    transition: background-color .2s ease;
    height: 68px;
  }

  .users-table tbody tr:hover {
    background-color: #FAFBFC;
  }

  .user-row {
    cursor: pointer;
    transition: background-color .18s ease;
  }

  .user-row:hover,
  .user-row:focus-visible {
    background-color: #FEF2F2;
    outline: none;
  }

  .user-cell-name {
    font-size: 15px;
    font-weight: 600;
    color: #0F172A;
    display: block;
    margin-bottom: 4px;
  }

  .user-cell-email {
    font-size: 14px;
    color: #94A3B8;
    font-weight: 400;
  }

  .status-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  /* Status Badges */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
  }

  .status-badge.online {
    background-color: #ECFDF5;
    color: #059669;
  }

  .status-badge.offline {
    background-color: #F1F5F9;
    color: #64748B;
  }

  .status-badge.new {
    background-color: #FFF7ED;
    color: #EA580C;
  }

  .status-badge.admin {
    background-color: #EEF2FF;
    color: #4F46E5;
  }

  .view-link {
    display: inline-flex;
    align-items: center;
    color: #166534;
    font-weight: 600;
    text-decoration: none;
    font-size: 13px;
    padding: 4px 8px;
    border-radius: 8px;
    transition: all .18s ease;
    cursor: pointer;
  }

  .view-link:hover {
    text-decoration: underline;
    background-color: rgba(22,101,52,.06);
  }

  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    gap: 4px;
    padding: 16px 24px;
    border-top: 1px solid #f3f4f6;
  }

  .pagination a,
  .pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    color: #374151;
    background-color: #ffffff;
    transition: all 0.2s ease;
  }

  .pagination a:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
  }

  .pagination .active {
    background-color: #14532d;
    color: #ffffff;
    border-color: #14532d;
  }

  .pagination .disabled {
    color: #d1d5db;
    cursor: not-allowed;
  }

  .modal-overlay {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0, 0, 0, 0.6);
    z-index: 300;
  }

  .modal-overlay.is-open {
    display: flex;
  }

  .modal-card {
    width: min(880px, 100%);
    max-height: 90vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.22);
    padding: 32px;
    position: relative;
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 1px solid #E5E7EB;
  }

  .modal-title {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #0F172A;
  }

  .modal-subtitle {
    margin: 4px 0 0;
    color: #64748B;
    font-size: 13px;
  }

  .modal-close {
    border: 0;
    background: transparent;
    color: #64748B;
    font-size: 24px;
    cursor: pointer;
  }

  .modal-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }

  .modal-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
  }

  .modal-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .modal-label {
    font-size: 13px;
    font-weight: 600;
    color: #1F2937;
  }

  .modal-select,
  .modal-input,
  .modal-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #D1D5DB;
    border-radius: 12px;
    font-size: 14px;
    color: #0F172A;
    background: #ffffff;
    box-sizing: border-box;
  }

  .modal-textarea {
    min-height: 110px;
    resize: vertical;
    font-family: inherit;
  }

  .modal-value {
    font-size: 14px;
    color: #0F172A;
    line-height: 1.6;
    word-break: break-word;
  }

  .modal-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 4px;
  }

  .modal-action {
    flex: 1;
    min-width: 120px;
    border-radius: 12px;
    padding: 12px 16px;
    border: 1px solid transparent;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .18s ease, transform .18s ease;
  }

  .modal-action-primary {
    background: #166534;
    color: #ffffff;
    border-color: transparent;
  }

  .modal-action-secondary {
    background: #ffffff;
    color: #0F172A;
    border-color: #D1D5DB;
  }

  .modal-action-secondary:hover {
    background: #F8FAFC;
  }

  .package-quick-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
  }

  .package-quick-btn {
    border: 1px solid #D1D5DB;
    background: #ffffff;
    color: #0F172A;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .18s ease;
  }

  .package-quick-btn:hover {
    background: #F8FAFC;
  }

  .toolbar-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .toolbar-btn {
    border: 1px solid #D1D5DB;
    background: #ffffff;
    color: #475569;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background .18s ease, transform .18s ease;
  }

  .toolbar-btn:hover {
    background: #F8FAFC;
  }

  .toolbar-btn-secondary {
    background: #F8FAFC;
    border-color: transparent;
  }

  .user-history-section {
    margin-top: 16px;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    background: #ffffff;
    overflow: hidden;
  }

  .user-history-section summary {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 18px;
    background: #F8FAFC;
    border: none;
    list-style: none;
  }

  .user-history-section summary::-webkit-details-marker {
    display: none;
  }

  .user-history-section summary::after {
    content: '\203A';
    transform: rotate(90deg);
    transition: transform 0.2s ease;
    color: #64748B;
    font-size: 18px;
  }

  .user-history-section[open] summary::after {
    transform: rotate(-90deg);
  }

  .user-history-list {
    display: grid;
    gap: 10px;
    padding: 16px 18px 18px;
  }

  .user-history-item {
    padding: 12px 14px;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    background: #F9FAFB;
  }

  .user-history-item strong {
    display: block;
    color: #111827;
    margin-bottom: 4px;
  }

  .user-history-meta {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    color: #64748B;
    font-size: 12px;
    flex-wrap: wrap;
  }

  .table-card-header {
    padding: 24px 32px;
    font-size: 16px;
    font-weight: 700;
    color: #0F172A;
  }

  .section-label {
    display: block;
    margin-bottom: 8px;
    color: #94A3B8;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
  }

  .section-title {
    margin: 0;
    font-size: 22px;
    font-weight: 800;
    color: #0F172A;
  }

  .section-subtitle {
    margin: 8px 0 0;
    color: #94A3B8;
    font-size: 14px;
  }

  .text-nowrap {
    white-space: nowrap;
  }

  .modal-full-width {
    grid-column: 1 / -1;
  }

  .error-list {
    margin: 0;
    padding-left: 18px;
  }

  .empty-message {
    margin: 0;
    color: #64748B;
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 40px 24px;
    color: #9ca3af;
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .summary-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .admin-nav {
      padding: 0;
      gap: 6px;
    }

    .admin-nav-btn {
      padding: 0 12px;
      height: 32px;
      font-size: 12px;
    }

    .admin-header {
      padding: 16px 12px;
      top: 48px;
    }

    .admin-header-content {
      flex-direction: column;
      align-items: flex-start;
    }

    .admin-header-actions {
      width: 100%;
      gap: 8px;
    }

    .header-btn {
      flex: 1;
      padding: 0 12px;
      height: 32px;
      font-size: 12px;
    }

    .admin-title {
      font-size: 20px;
    }

    .admin-content {
      padding: 16px;
    }

    .summary-grid {
      grid-template-columns: 1fr;
    }

    .users-panel-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .search-box {
      width: 100%;
    }

    .search-box form {
      flex-direction: column;
    }

    .users-table {
      font-size: 12px;
    }

    .users-table th,
    .users-table td {
      padding: 10px 12px;
    }

    .status-badges {
      gap: 4px;
    }

    .status-badge {
      padding: 3px 8px;
      font-size: 10px;
    }

    .summary-card {
      padding: 16px;
    }

    .summary-value {
      font-size: 28px;
    }
  }

  @media (max-width: 480px) {
    .admin-shell {
      padding: 0;
    }

    .admin-nav {
      overflow-x: auto;
      padding: 0;
    }

    .admin-nav-btn {
      flex-shrink: 0;
      padding: 0 10px;
      height: 30px;
      font-size: 11px;
    }

    .admin-header {
      padding: 16px 12px;
    }

    .admin-title {
      font-size: 18px;
    }

    .admin-copy {
      font-size: 12px;
    }

    .admin-content {
      padding: 12px;
    }

    .users-table th {
      font-size: 10px;
    }

    .users-table td {
      padding: 8px 10px;
      font-size: 11px;
    }

    .user-cell-name {
      font-size: 12px;
    }

    .user-cell-email {
      font-size: 11px;
    }

    .summary-card {
      padding: 16px;
    }

    .summary-value {
      font-size: 28px;
    }
  }
</style>
