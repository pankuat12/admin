var _WEB_ROOT_ = window.location.origin + "/admin";
var chkmail =
    /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/,
  err = {
    background: "#dc3545",
  },
  apr = {
    background: "#198754",
  };
let toastInstance = null;
function validateField(field, errorMsg) {
  if (!field.val().trim()) {
    field.addClass("is-invalid");
    if (toastInstance) toastInstance.hideToast();
    toastInstance = Toastify({
      gravity: "bottom",
      position: "center",
      text: errorMsg,
      duration: 3000,
      style: { background: "#dc3545" },
    });
    toastInstance.showToast();
    return false;
  } else {
    field.removeClass("is-invalid");
    return true;
  }
}
function startCountdown(duration, displayElement, callback) {
  var countDownDate = new Date().getTime() + duration;
  var x = setInterval(function () {
    var now = new Date().getTime();
    var distance = countDownDate - now;
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    var countdownText = "" + minutes + "m " + seconds + "s ";
    displayElement.html(countdownText);
    if (distance < 0) {
      clearInterval(x);
      displayElement.html("Time is up!");
      if (typeof callback === "function") {
        callback();
      }
    }
  }, 1000);
}
function imageChangeData(data) {
  let fileInput = $(".typeIcon" + data)[0];
  if (fileInput && fileInput.files && fileInput.files.length > 0) {
    let reader = new FileReader();
    reader.onload = (e) => {
      $("#preview-image" + data).attr("src", e.target.result);
      $("#imagBase64-image" + data).val(e.target.result);
    };
    reader.readAsDataURL(fileInput.files[0]);
  } else {
    console.error("File input element not found or no files selected.");
  }
}
$(".aliasObj").keyup(function () {
  var obj = $(".aliasObj").val();
  obj = obj.toLowerCase();
  var obj = obj.replace(/[^a-zA-Z0-9]+/g, "-");
  $(".aliasSub").val(obj);
});
var mailReg =
  /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
// only number
$(".onlyInt").keypress(function (event) {
  if (event.which < 29 || event.which > 57) {
    event.preventDefault();
    var currentValue = $(this).val();
    $(this).val(currentValue.replace(/[^0-9]/g, ""));
  }
});
// common delete js
$(document).on("click", ".delete", function () {
  $("#csd").modal("show");
  var call = $(this).data("call");
  var pick = $(this).data("pick");
  $("#call").val(call);
  $("#pick").val(pick);
});
$(document).on("click", ".confirm", function () {
  var call = $("#call").val();
  var pick = $("#pick").val();
  $.ajax({
    type: "POST",
    url: _WEB_ROOT_ + "/delete",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      call: call,
      pick: pick,
    },
    success: function (data) {
      $("#csd").modal("hide");
      response = JSON.parse(data);
      Toastify({
        gravity: "top",
        position: "right",
        text: response.message,
        duration: 1500,
        style: response.state == 1 ? { background: "#198754" } : { background: "#dc3545" },
      }).showToast();
      setTimeout(function () {
        window.location.reload(1);
      }, 500);
    },
  });
});
$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  });
  // check
  $(".ctoas").click(function () {
    $(this).parent("div").parent("div").remove();
  });
  $(".otpContainer")
    .find(".otpDigit")
    .each(function () {
      $(this).attr("maxlength", 1);
      $(this).on("keyup", function (e) {
        var parent = $($(this).parent());
        if (e.keyCode === 8 || e.keyCode === 37) {
          var prev = parent.find("input#" + $(this).data("previous"));
          if (prev.length) {
            $(prev).select();
          }
        } else if (
          (e.keyCode >= 48 && e.keyCode <= 57) ||
          (e.keyCode >= 65 && e.keyCode <= 90) ||
          (e.keyCode >= 96 && e.keyCode <= 105) ||
          e.keyCode === 39
        ) {
          var next = parent.find("input#" + $(this).data("next"));
          if (next.length) {
            $(next).select();
          } else {
            if (parent.data("autosubmit")) {
              parent.submit();
            }
          }
        }
      });
    });
  $(".checkIt").click(function () {
    var userMail = $("#userMail").val();
    if (!userMail) {
      $("#userMail").addClass("is-invalid");
      if ($("#userMail").next("span").length) {
        $("#userMail").next("span").remove();
      }
      $("#userMail").after(
        '<span class="text-danger" >Please Mention Mail Address !</span>',
      );
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-left",
        timeOut: 1500,
      };
      toastr.warning("Please Mention Mail Address !", "warning");
      return false;
    } else {
      if ($("#userMail").next("span").length) {
        $("#userMail").next("span").remove();
      }
      $("#userMail").removeClass("is-invalid");
    }
    if (!mailReg.test(userMail)) {
      $("#userMail").addClass("is-invalid");
      if ($("#userMail").next("span").length) {
        $("#userMail").next("span").remove();
      }
      $("#userMail").after(
        '<span class="text-danger" >Mail Not In Format !</span>',
      );
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-center",
        timeOut: 1200,
      };
      toastr.warning("Mail Not In Format !", "warning");
      return false;
    } else {
      if ($("#userMail").next("span").length) {
        $("#userMail").next("span").remove();
      }
      $("#userMail").removeClass("is-invalid");
    }
    $.ajax({
      type: "POST",
      url: _WEB_ROOT_ + "/checkParameters",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: {
        userMail: userMail,
      },
      beforeSend: function () {
        $(".checkIt").prop("disabled", true);
        $(".checkIt").html(
          '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait',
        );
      },
      success: function (data) {
        // $('.checkIt').prop('disabled', true);
        // $('.checkIt').html('Wait <span class="spinner-border spinner-border-sm me-2" role="status"></span>');
        response = JSON.parse(data);
        if (response.status == 1) {
          // send otp
          $.ajax({
            type: "POST",
            url: _WEB_ROOT_ + "/sendOtp",
            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
              userMail: response.data,
            },
            success: function (data) {
              response = JSON.parse(data);
              if (response.status == 1) {
                toastr.options = {
                  closeButton: true,
                  progressBar: true,
                  positionClass: "toast-top-center",
                  timeOut: 1200,
                };
                toastr.success("OTP Send Successfully !", "success");
                $(".sdo").addClass("d-none");
                $(".cso").removeClass("d-none");
                // count
                startCountdown(180000, $("#timer"), function () {
                  $("#resendOtp").removeClass("d-none");
                  $("#verifyotp").addClass("d-none");
                });
                $("#accessToken").val(response.authCode);
                $("#mail_n_velidate").val(userMail);
              } else {
                swal.fire({
                  title: response.message,
                  icon: "warning",
                });
              }
            },
          });
        } else {
          $(".checkIt").prop("disabled", false);
          $(".checkIt").html(
            'NEXT <i class="ms-2 fa fa-arrow-right" aria-hidden="true"></i>',
          );
          swal.fire({
            title: response.message,
            icon: "warning",
          });
        }
      },
    });
  });
  $("#verifyotp").click(function () {
    var digit1 = $("#digit-1").val();
    var digit2 = $("#digit-2").val();
    var digit3 = $("#digit-3").val();
    var digit4 = $("#digit-4").val();
    accessToken = $("#accessToken").val();
    mail = $("#mail_n_velidate").val();
    if (!digit1) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-center",
        timeOut: 1200,
      };
      toastr.warning("Please Entry Otp!", "warning");
      $("#digit-1").addClass("is-invalid");
      return false;
    } else {
      $("#digit-1").removeClass("is-invalid");
    }
    if (!digit2) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-center",
        timeOut: 1200,
      };
      toastr.warning("Please Entry Otp!", "warning");
      $("#digit-2").addClass("is-invalid");
      return false;
    } else {
      $("#digit-2").removeClass("is-invalid");
    }
    if (!digit3) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-center",
        timeOut: 1200,
      };
      toastr.warning("Please Entry Otp!", "warning");
      $("#digit-3").addClass("is-invalid");
      return false;
    } else {
      $("#digit-3").removeClass("is-invalid");
    }
    if (!digit4) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-center",
        timeOut: 1200,
      };
      toastr.warning("Please Entry Otp!", "warning");
      $("#digit-4").addClass("is-invalid");
      return false;
    } else {
      $("#digit-4").removeClass("is-invalid");
    }
    otp = digit1 + digit2 + digit3 + digit4;
    $.ajax({
      type: "POST",
      url: _WEB_ROOT_ + "/validateOtp",
      data: {
        accessToken: accessToken,
        mail: mail,
        otp: otp,
      },
      beforeSend: function () {
        $("#verifyotp").prop("disabled", true);
        $("#verifyotp").html(
          '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait',
        );
      },
      success: function (data) {
        response = JSON.parse(data);
        if (response.status == 1) {
          toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-center",
            timeOut: 1200,
          };
          toastr.success(response.message, "success");
          setTimeout(function () {
            window.location.href = "/dashboard";
          }, 1300);
        } else {
          $("#verifyotp").prop("disabled", false);
          $("#verifyotp").html(
            'Verify Your OTP <i class="ms-2 fa fa-arrow-right" aria-hidden="true"></i>',
          );
          toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-center",
            timeOut: 1200,
          };
          toastr.warning(response.message, "warning");
        }
      },
    });
  });
  $("#resendOtp").click(function () {
    userMail = $("#mail_n_velidate").val();
    $.ajax({
      type: "POST",
      url: _WEB_ROOT_ + "/sendOtp",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: {
        userMail: userMail,
      },
      beforeSend: function () {
        $("#resendOtp").prop("disabled", true);
      },
      success: function (data1) {
        response = JSON.parse(data1);
        if (response.status == 1) {
          $("#verifyotp").removeClass("d-none");
          $("#resendOtp").addClass("d-none");
          $("#resendOtp").prop("disabled", false);
          startCountdown(180000, $("#timer"), function () {
            $("#resendOtp").removeClass("d-none");
            $("#verifyotp").addClass("d-none");
          });
          $("#accessToken").val(response.authCode);
          $("#mail_n_velidate").val(userMail);
          toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-center",
            timeOut: 1200,
          };
          toastr.success("OTP Send Successfully !", "success");
        } else {
          toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-center",
            timeOut: 1200,
          };
          toastr.warning(response.message, "warning");
        }
      },
    });
  });
  // global img per
  $(".subImg").change(function () {
    let reader = new FileReader();
    let $input = $(this);
    let $img = $input.closest("parentPerImg").find(".objImg");
    reader.onload = (e) => {
      $img.removeClass("d-none");
      $img.attr("src", e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  });

  $(".subAImg").change(function () {
    let reader = new FileReader();
    reader.onload = (e) => {
      $(".objAImg").removeClass("d-none");
      $(".objAImg").attr("src", e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  });
  $(document).on("change", ".subBImg", function () {
    let reader = new FileReader();
    reader.onload = (e) => {
      $(".objBImg").removeClass("d-none");
      $(".objBImg").attr("src", e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  });
  $(".subCImg").change(function () {
    let reader = new FileReader();
    reader.onload = (e) => {
      $(".objCImg").removeClass("d-none");
      $(".objCImg").attr("src", e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  });
});
