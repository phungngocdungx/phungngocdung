@extends('layouts.app')
@section('title', 'Quản lý tài khoản admin')
@section('content')
    <div class="content">
        <h2 class="mb-2 lh-sm">Quản lý tài khoản admin</h2>
        <p class="text-body-tertiary lead mb-2">Quản lý tất cả tài khoản admin hệ thống & cấp quyền truy cập</p>
        <div class="mt-4">
            <div class="row g-4">
                <div class="col-12 col-xl-12 order-1 order-xl-0">
                    <div class="mb-9">
                        <div class="card shadow-none border mb-3" data-component-card="data-component-card">
                            <div class="card-header p-4 border-bottom bg-body">
                                <div class="row g-3 justify-content-between align-items-center">
                                    <div class="col-12 col-md">
                                        <h4 class="text-body mb-0" data-anchor="data-anchor">Admin</h4>
                                    </div>
                                    <div class="col col-md-auto">
                                        <nav class="nav justify-content-end doc-tab-nav align-items-center" role="tablist">
                                            <button class="btn btn-link px-2 text-body copy-code-btn" type="button"><span
                                                    class="fas fa-copy me-1"></span>Copy
                                                Code</button><a class="btn btn-sm btn-phoenix-primary code-btn ms-2"
                                                data-bs-toggle="collapse" href="#example-code" role="button"
                                                aria-controls="example-code" aria-expanded="false"> <span class="me-2"
                                                    data-feather="code"></span>View code</a><a
                                                class="btn btn-sm btn-phoenix-primary preview-btn ms-2"><span class="me-2"
                                                    data-feather="eye"></span>Hide code</a>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="collapse code-collapse" id="example-code">
                                    <pre class="scrollbar" style="max-height:420px">
                                        <code class="language-html">1234
                                        </code>
                                    </pre>
                                </div>
                                <div class="p-4 code-to-copy">
                                    <div class="d-flex align-items-center justify-content-end my-3">
                                        <div id="bulk-select-replace-element">
                                            <button class="btn btn-phoenix-success btn-sm" type="button">
                                                <span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span>
                                                <span class="ms-1">New</span>
                                            </button>
                                        </div>
                                        <div class="d-none ms-3" id="bulk-select-actions">
                                            <div class="d-flex">
                                                <select class="form-select form-select-sm" aria-label="Bulk actions">
                                                    <option selected="selected">Bulk actions</option>
                                                    <option value="Delete">Delete</option>
                                                    <option value="Archive">Archive</option>
                                                </select>
                                                <button class="btn btn-phoenix-danger btn-sm ms-2"
                                                    type="button">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tableExample"
                                        data-list='{"valueNames":["name","email","age"],"page":5,"pagination":true}'>
                                        <div class="table-responsive mx-n1 px-1">
                                            <table class="table table-sm border-top border-translucent fs-9 mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="white-space-nowrap fs-9 align-middle ps-0"
                                                            style="max-width:20px; width:18px;">
                                                            <div class="form-check mb-0 fs-8"><input
                                                                    class="form-check-input" id="bulk-select-example"
                                                                    type="checkbox"
                                                                    data-bulk-select='{"body":"bulk-select-body","actions":"bulk-select-actions","replacedElement":"bulk-select-replace-element"}' />
                                                            </div>
                                                        </th>
                                                        <th class="sort align-middle ps-3" data-sort="name">Name
                                                        </th>
                                                        <th class="sort align-middle" data-sort="email">Email</th>
                                                        <th class="sort align-middle" data-sort="age">Age</th>
                                                        <th class="sort text-end align-middle pe-0" scope="col">
                                                            ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list" id="bulk-select-body">
                                                    @foreach ($managers as $manager)
                                                        <tr>
                                                            <td class="fs-9 align-middle">
                                                                <div class="form-check mb-0 fs-8">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        data-bulk-select-row="{&quot;name&quot;:&quot;Anna&quot;,&quot;email&quot;:&quot;anna@example.com&quot;,&quot;age&quot;:18}" />
                                                                </div>
                                                            </td>
                                                            <td class="align-middle ps-3 name">{{ $manager->name }}</td>
                                                            <td class="align-middle email">{{ $manager->email }}</td>
                                                            <td class="align-middle age">{{ $manager->age }}</td>
                                                            <td class="align-middle white-space-nowrap text-end pe-0">
                                                                <div class="btn-reveal-trigger position-static"><button
                                                                        class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        data-boundary="window" aria-haspopup="true"
                                                                        aria-expanded="false"
                                                                        data-bs-reference="parent"><span
                                                                            class="fas fa-ellipsis-h fs-10"></span></button>
                                                                    <div class="dropdown-menu dropdown-menu-end py-2"><a
                                                                            class="dropdown-item" href="#!">View</a><a
                                                                            class="dropdown-item" href="#!">Export</a>
                                                                        <div class="dropdown-divider"></div><a
                                                                            class="dropdown-item text-danger"
                                                                            href="#!">Remove</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex flex-between-center pt-3 mb-3">
                                            <div class="pagination d-none"></div>
                                            <p class="mb-0 fs-9">
                                                <span class="d-none d-sm-inline-block"
                                                    data-list-info="data-list-info"></span>
                                                <span class="d-none d-sm-inline-block"> &mdash; </span>
                                                <a class="fw-semibold" href="#!" data-list-view="*">
                                                    View all
                                                    <span class="fas fa-angle-right ms-1"
                                                        data-fa-transform="down-1"></span>
                                                </a><a class="fw-semibold d-none" href="#!" data-list-view="less">
                                                    View Less
                                                    <span class="fas fa-angle-right ms-1"
                                                        data-fa-transform="down-1"></span>
                                                </a>
                                            </p>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    data-list-pagination="prev"><span>Previous</span></button>
                                                <button class="btn btn-sm btn-primary px-4 ms-2" type="button"
                                                    data-list-pagination="next"><span>Next</span></button>
                                            </div>
                                        </div>
                                        <p class="mb-2">Click the button to get selected rows</p><button
                                            class="btn btn-warning" data-selected-rows="data-selected-rows">Get
                                            Selected Rows</button>
                                        <pre id="selectedRows"></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
            <div class="toast align-items-center text-white bg-dark border-0" id="icon-copied-toast" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex" data-bs-theme="dark">
                    <div class="toast-body p-3"></div><button class="btn-close me-2 m-auto" type="button"
                        data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
@endsection
