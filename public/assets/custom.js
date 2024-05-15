// JavaScript Document
function _loader(action, msg) {
    let $div = jQuery("#preloader");
    if (action) $div.show();
    else $div.fadeOut();
    // $div.find("p").html(msg != undefined && msg ? msg : "Please wait...");
}
function copyToClipboard(text) {
    var dummy = document.createElement("textarea");
    // to avoid breaking orgain page when copying more words
    // cant copy when adding below this code
    // dummy.style.display = 'none'
    document.body.appendChild(dummy);
    //Be careful if you use texarea. setAttribute('value', value), which works with "input" does not work with "textarea". – Eduard
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
    showAlert(1, "Text Copied ...");
}
function uniqueId(length) {
    var result = "";
    var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (var i = 0; i < length; i++)
        result += characters.charAt(
            Math.floor(Math.random() * characters.length)
        );
    return result;
}
function mergeObj(obj1, obj2) {
    obj1 = obj1 == undefined ? {} : obj1;
    obj2 = obj2 == undefined ? {} : obj2;
    return Object.assign(obj1, obj2);
}

// Alerts
function showAlert(status, message) {
    if (message) {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
          }
          if(status){
            toastr["success"](message)
          } else{
            toastr["error"](message)
          }
    }
}
function ajaxSucc(res) {
    if (res.message) showAlert(res.status, res.message);
}
function ajaxErr(res) {
    if (res.statusText != "abort") {
        if (
            res.responseJSON != undefined &&
            res.responseJSON.message != undefined
        ) {
            showAlert(0, res.responseJSON.message);
        } else {
            showAlert(0, res.statusText);
        }
    }
}

// Select2
function applySelect2(eles) {
    if (eles.length) {
        eles.each(function () {
            if (jQuery(this).hasClass("select2-hidden-accessible")) {
                jQuery(this).select2("destroy");
            }
            jQuery(this).select2({
                width: "100%",
                allowClear: true,
                placeholder: "Please select",
                dropdownParent: $(this).parent(),
            });
        });
    }
}
function refreshSelect2(ele) {
    ele.select2("destroy");
    applySelect2(ele);
}
function createOption(ele, data, selectedOption = []) {
    $.each(data, function (key, value) {
        if (selectedOption.length > 0) {
            var selectedvalues = selectedOption.includes(value.id.toString())
                ? "selected"
                : "";
        }

        $(ele).append(
            `<option value="${value.id}" ${selectedvalues}>${value.name}</option>`
        );
    });
}

// DataTable Functions
var dtTable;
function applyDataTable(tableId, url, params, searchFun = {}) {
    var searchData = "";
    if (searchFun && searchFun != "") {
        searchData = searchFun;
    } else {
        searchData = "";
    }
    return jQuery(tableId).DataTable(
        Object.assign(
            {
                responsive: true,
                pageLength: 10,
                paging: true,
                processing: true,
                serverSide: true,
                // searchDelay: 1000,
                stateSave: false,
                lengthMenu: [10, 25, 50, 75, 100],
                language: {
                    paginate: {
                      next: '<i class="fa fa-angle-right" aria-hidden="true"></i>', // or '→'
                      previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>' // or '←'
                    }
                  },
                ajax: {
                    url: url,
                    type: "POST",
                    data: searchData,
                    dataSrc: function (json) {
                        return json.data;
                    },
                },
                // Setup Counters
                drawCallback: function (settings) {
                    // var table = this.api();
                    // dataTableSetCounter(table);
                },
                initComplete: function (settings, json) {},
            },
            params
        )
    );
}
function refreshDtTable() {
    dtTable.draw(false);
    // jQuery('.crud-check').prop('checked', false);
}
function mergeCrudSearch(d) {
    var extra = {};
    if (jQuery("#frm-search-crud").length) {
        var temp = jQuery("#frm-search-crud").serializeArray();
        for (var i in temp) extra[temp[i].name] = temp[i].value;
    }
    return Object.assign(d, extra);
}
function fireCrudSearch() {
    refreshDtTable();
}
function clearCrudSearch() {
    jQuery("#frm-search-crud input:text").val("");
    refreshDtTable();
}
function crudMultiCheck(ele) {
    jQuery(".chk-multi-check").prop("checked", jQuery(ele).is(":checked"));
}
function crudMultiCheckIds() {
    var arr = [];
    jQuery(".chk-multi-check:checked").each(function (index, element) {
        arr.push(jQuery(this).val());
    });
    return arr;
}
function crudDelete(url) {
    // var ids = crudMultiCheckIds();
    // if (!ids.length) {
    // 	showAlert(0, 'Please select at least one record.');
    // 	return;
    // }
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this deleted record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
            // preConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                _loader(1);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    // data: { ids: ids },
                    success: function (res) {
                        ajaxSucc(res);
                        _loader(0);
                        if (res.status) {
                            if (dtTable != undefined) refreshDtTable();
                            $(".modal_close_ajax").modal("hide");
                            // swal("Deleted!", res.message, "success");
                        } else {
                            // swal("Failed", res.message, "error");
                        }
                    },
                    error: function (res) {
                        ajaxErr(res);
                        _loader(0);
                    },
                });
            }
        }
    );
}
function crudApprove(url) {
    // var ids = crudMultiCheckIds();
    // if (!ids.length) {
    // 	showAlert(0, 'Please select at least one record.');
    // 	return;
    // }
    swal({
            title: "Are you sure?",
            text: "Approve this user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, Approve it!",
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
            // preConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                _loader(1);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    // data: { ids: ids },
                    success: function (res) {
                        ajaxSucc(res);
                        _loader(0);
                        if (res.status) {
                            if (dtTable != undefined) refreshDtTable();
                            $(".modal_close_ajax").modal("hide");
                            // swal("Deleted!", res.message, "success");
                        } else {
                            // swal("Failed", res.message, "error");
                        }
                    },
                    error: function (res) {
                        ajaxErr(res);
                        _loader(0);
                    },
                });
            }
        }
    );
}
function crudReject(url) {
    // var ids = crudMultiCheckIds();
    // if (!ids.length) {
    // 	showAlert(0, 'Please select at least one record.');
    // 	return;
    // }
    swal({
            title: "Are you sure?",
            text: "reject this user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, Reject it!",
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
            // preConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                _loader(1);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    // data: { ids: ids },
                    success: function (res) {
                        ajaxSucc(res);
                        _loader(0);
                        if (res.status) {
                            if (dtTable != undefined) refreshDtTable();
                            $(".modal_close_ajax").modal("hide");
                            // swal("Deleted!", res.message, "success");
                        } else {
                            // swal("Failed", res.message, "error");
                        }
                    },
                    error: function (res) {
                        ajaxErr(res);
                        _loader(0);
                    },
                });
            }
        }
    );
}
function crudBlock(url) {
    // var ids = crudMultiCheckIds();
    // if (!ids.length) {
    // 	showAlert(0, 'Please select at least one record.');
    // 	return;
    // }
    swal({
            title: "Are you sure?",
            text: "block this user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, Block it!",
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
            // preConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                _loader(1);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    // data: { ids: ids },
                    success: function (res) {
                        ajaxSucc(res);
                        _loader(0);
                        if (res.status) {
                            if (dtTable != undefined) refreshDtTable();
                            $(".modal_close_ajax").modal("hide");
                            // swal("Deleted!", res.message, "success");
                        } else {
                            // swal("Failed", res.message, "error");
                        }
                    },
                    error: function (res) {
                        ajaxErr(res);
                        _loader(0);
                    },
                });
            }
        }
    );
}
function crudStatusChange(url, type) {
    var ids = crudMultiCheckIds();
    if (!ids.length) {
        showAlert(0, "Please select at least one record.");
        return;
    }

    var text = (btnText = "");
    if (type == 1) {
        text = "You want to activate selected records!";
        btnText = "Active";
    } else if (type == 0) {
        text = "You want to deactivate selected records!";
        btnText = "Inactive";
    }
    Swal.fire({
        title: "Are you sure?",
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: btnText,
    }).then((result) => {
        if (result.value) {
            _loader(1);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: { ids: ids, type: type },
                success: function (res) {
                    if (crudTable != undefined) refreshDtTable();
                    ajaxSucc(res);
                    _loader(0);
                },
                error: function (res) {
                    ajaxErr(res);
                    _loader(0);
                },
            });
        }
    });
}
function crudUpdateOrder(url) {
    var arr = {};
    jQuery(".list-order-rows").each(function (index, element) {
        arr[jQuery(this).data("dbid")] = jQuery(this).val();
    });
    _loader(1);
    jQuery.ajax({
        type: "POST",
        url: url,
        data: { data: arr },
        success: function (res) {
            if (crudTable != undefined) refreshDtTable();
            ajaxSucc(res);
            _loader(0);
        },
        error: function (res) {
            ajaxErr(res);
            _loader(0);
        },
    });
}
function checkUnique(type) {
    var v = jQuery.trim(jQuery("#" + type).val());
    if (v) {
        _loader(1, "Checking unique...");
        jQuery.ajax({
            type: "POST",
            url: urlCheckUnique,
            data: { slug: v, type: type },
            success: function (res) {
                //ajaxSucc(res);
                jQuery("#" + type).val(res.slug);
                if (type == "v_sku") jQuery("#uniqueInfoSku").html(res.text);
                else jQuery("#uniqueInfo").html(res.text);
                _loader(0);
            },
            error: function (res) {
                ajaxErr(res);
                _loader(0);
            },
        });
    }
}

function crudSimpleSave(form) {
    _loader(1);

    var fieldArr = jQuery(form).serializeArray();
    var frmData = new FormData();
    for (let index in fieldArr) {
        frmData.append(fieldArr[index].name, fieldArr[index].value);
    }
    if (jQuery(form).find('input[type="file"]').length) {
        jQuery(form)
            .find('input[type="file"]')
            .each(function () {
                let name = jQuery(this).attr("name");
                let files = jQuery(this)[0].files;
                if (files.length) {
                    for (let index in files) {
                        frmData.append(name, files[index]);
                    }
                }
            });
    }

    jQuery.ajax({
        type: "POST",
        url: jQuery(form).attr("action"),
        data: frmData,
        dataType: "json",
        mimeType: "multipart/form-data",
        contentType: false,
        processData: false,
        success: function (res) {
            ajaxSucc(res);
            if (res.status) {
                if (res.url != undefined && res.url) {
                    setTimeout(() => {
                        location.href = res.url;
                    }, 2000);
                }
                if (res.tab != undefined && res.tab) {
                    setTimeout(() => {
                        location.href = "?tab=" + res.tab;
                    }, 2000);
                }
            }
            _loader(0);
        },
        error: function (res) {
            ajaxErr(res);
            _loader(0);
        },
    });
    return false;
}

function applySimpleDataTable(tableId, url, params, cb, searchFun = {}) {
    var searchData = "";
    if (searchFun && searchFun != "") {
        searchData = searchFun;
    } else {
        searchData = "";
    }
    return jQuery(tableId).DataTable(
        Object.assign(
            {
                responsive: true,
                pageLength: 10,
                paging: true,
                processing: true,
                serverSide: true,
                stateSave: false,
                lengthMenu: [10, 25, 50, 75, 100],
                ajax: {
                    type: "POST",
                    url: url,
                    data: searchData,
                },
                // Setup Counters
                drawCallback: function (settings) {
                    if (cb != undefined) {
                        cb(settings);
                    }
                },
                initComplete: function (settings, json) {},
            },
            params
        )
    );
}
function simpleDataTableRefresh(obj) {
    obj.ajax.reload();
}

function modalForm(modalId, url, cb, docId = 0) {
    _loader(1);
    jQuery.ajax({
        type: "GET",
        url: url,
        success: function (res) {
            _loader(0);
            if (res.status) {
                console.log(res);
                jQuery(modalId).find(".modal-title").html(res.title);
                jQuery(modalId).find(".modal-body").html(res.body);
                applySelect2(jQuery(modalId).find(".select2"));
                applyDates("date", jQuery(modalId).find(".datepicker"));
                if (jQuery(".txteditor").length)
                    jQuery(".txteditor").summernote({
                        // toolbar: [
                        //     ["style", ["bold", "italic", "underline"]],
                        //     ["fontname", ["fontname"]],
                        //     [("fontsize", ["fontsize"])],
                        //     ["color", ["color"]],
                        //     ["para", ["ul", "ol", "paragraph"]],
                        // ],
                        height: 150,
                    });
                if (docId) {
                    $("#document_list").select2("val", [docId]);
                }
                jQuery(modalId).modal("show");
            } else {
                ajaxSucc(res);
            }
            cb(res.status);
        },
        error: function (res) {
            _loader(0);
            ajaxErr(res);
            cb(0);
        },
    });
}

function confirmDelete(cb) {
    swal(
        {
            title: "Are you sure?",
            text: "You will not be able to recover this deleted record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
        },
        function (isConfirm) {
            if (isConfirm) {
                cb(1);
            }
        }
    );
}

function taskDone(cb) {
    swal(
        {
            title: "Task Completed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, Task Completed!",
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
        },
        function (isConfirm) {
            if (isConfirm) {
                cb(1);
            }
        }
    );
}

// Form Validations
function formValidate(id, params) {
    params = params != undefined ? params : {};

    params.highlight = function (input) {
        jQuery(input).parents(".form-line").addClass("error");
    };
    params.unhighlight = function (input) {
        jQuery(input).parents(".form-line").removeClass("error");
    };
    params.errorPlacement = function (error, element) {
        if (jQuery(element).parents(".col-lg-6").length) {
            jQuery(element).parents(".col-lg-6").append(error);
        } else if (jQuery(element).parents(".col-lg-8").length) {
            jQuery(element).parents(".col-lg-8").append(error);
        }else if (jQuery(element).parents(".col-lg-4").length) {
            jQuery(element).parents(".col-lg-4").append(error);
        }
    };
    jQuery(id).validate(params);
}
function formValidateAjax(id, params, cb) {
    params.submitHandler = cb;
    if (jQuery(id).attr("data-with-tabs") == 1) {
        params.ignore = "";
        params.invalidHandler = function (form, validator) {
            if (validator.numberOfInvalids()) {
                jQuery(
                    "#a_" +
                        jQuery(validator.errorList[0].element)
                            .closest(".tab-pane")
                            .attr("id")
                ).click();
            }
        };
    }
    formValidate(id, params);
}

// Textare auto growth
function applyAutoSize(eles) {
    if (eles.length) {
        autosize(eles);
    }
}

// Datetimepicker plugin
function applyDates(type, eles) {
    if (eles.length) {
        if (type == "datetime") {
            eles.bootstrapMaterialDatePicker({
                format: "DD-MM-YYYY HH:mm",
                clearButton: true,
                weekStart: 1,
            });
        } else if (type == "date") {
            eles.bootstrapMaterialDatePicker({
                format: "DD-MM-YYYY",
                clearButton: true,
                weekStart: 1,
                time: false,
            });
        } else if (type == "time") {
            eles.bootstrapMaterialDatePicker({
                format: "HH:mm",
                clearButton: true,
                date: false,
            });
        }
    }
}

jQuery(document).ready(function (e) {
    // Setting CSRF Token
    jQuery.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
        },
        data: {},
        statusCode: {
            //200: function(xhr) { ajaxInvalidAccess(xhr) /*if(window.console) console.log(xhr.responseText);*/ },
            //400: function(xhr) { showGritter({ status: 0, msg: 'Bad Request' }); _loader(0); },
            //401: function(xhr) { showGritter({ status: 0, msg: 'Unauthorized Request - You are not logged in.' }); _loader(0); },
            //403: function(xhr) { showGritter({ status: 0, msg: 'Access Forbidden' }); _loader(0); },
            //404: function(xhr) { showGritter({ status: 0, msg: 'Request Not Found' }); _loader(0); },
            //405: function(xhr) { showGritter({ status: 0, msg: 'Method Not Allowed' }); _loader(0); },
            //406: function(xhr) { showGritter({ status: 0, msg: 'Not Acceptable' }); _loader(0); },
            //407: function(xhr) { showGritter({ status: 0, msg: 'Proxy Authentication Required' }); _loader(0); },
            //408: function(xhr) { showGritter({ status: 0, msg: 'Request Timeout' }); _loader(0); },
            //500: function(xhr) { showGritter({ status: 0, msg: 'Internal Server Error' }); _loader(0); },
            //502: function(xhr) { showGritter({ status: 0, msg: 'Bad Gateway' }); _loader(0); },
            //504: function(xhr) { showGritter({ status: 0, msg: 'Gateway Timeout' }); _loader(0); },
        },
    });

    // ////////////////////////////////////////////////////////////////////////////

    // // DataTable
    // jQuery(document).on('submit', '#frm-search-crud', function(){
    // 	fireCrudSearch(); return false;
    // });
    // jQuery(document).on('change', '#frm-search-crud select', function(){
    // 	jQuery('#crud-table').DataTable().draw(false);
    // });

    // ////////////////////////////////////////////////////////////////////////////

    // //Bootstrap Duallistbox
    // if(jQuery('.duallistbox').length) jQuery('.duallistbox').bootstrapDualListbox({
    // 	moveOnSelect: false
    // });

    // ////////////////////////////////////////////////////////////////////////////

    if (jQuery(".select2").length) applySelect2(jQuery(".select2"));
    if (jQuery(".txteditor").length)
        jQuery(".txteditor").summernote({
            height: 150,
        });

    if (jQuery(".cls-form-validate").length) {
        jQuery(".cls-form-validate").each(function () {
            formValidate("#" + jQuery(this).attr("id"));
        });
    }
    if (jQuery(".cls-crud-simple-save").length) {
        jQuery(".cls-crud-simple-save").each(function () {
            formValidateAjax("#" + jQuery(this).attr("id"), {}, crudSimpleSave);
        });
    }

    applyAutoSize(jQuery("textarea.auto-growth"));
    applyDates("datetime", jQuery(".datetimepicker"));
    applyDates("date", jQuery(".datepicker"));
    applyDates("time", jQuery(".timepicker"));

    if (msgSucc) showAlert(1, msgSucc);
    if (msgErr) showAlert(0, msgErr);
    $(".content-body").css("min-height", $(window).height() * 0.92 + "px");
    _loader(1);
});

$("body").on("focus", ".datetimepicker", function () {
    applyDates("datetime", jQuery(".datetimepicker"));
});
$("body").on("focus", ".datepicker", function () {
    applyDates("date", jQuery(".datepicker"));
});
$("body").on("focus", ".timepicker", function () {
    applyDates("time", jQuery(".timepicker"));
});
