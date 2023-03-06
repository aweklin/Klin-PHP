// plugins
(function ($) {
  window.getApiResponse = (response) => {
    if (response.hasOwnProperty("requestSuccessful")) {
      if (!response.requestSuccessful)
        return {
          hasError: true,
          message: response.responseMessage,
          data: null,
        };

      return {
        hasError: false,
        message: response.responseMessage,
        data: response.responseBody,
      };
    }

    if (response.hasOwnProperty("error")) {
      if (response.error_description)
        return {
          hasError: true,
          message: response.error_description,
          data: null,
        };

      return { hasError: true, message: response.error };
    }

    return response;
  };

  window.noneGetAsync = async function (
    type,
    url,
    payload,
    headers = { "Content-type": "application/json; charset=UTF-8" }
  ) {
    try {
      const request = await fetch(url, {
        method: type,
        body: JSON.stringify(payload),
        headers: headers,
        mode: "cors",
        cache: "no-cache",
        credentials: "same-origin",
      });
      const status = await request.status;
      if ([404].includes(status))
        return { hasError: true, message: `${url} not found.`, data: null };

      var response = await request.json();

      return getApiResponse(response);
    } catch (error) {
      return { hasError: true, message: error.message, data: null };
    }
  };

  window.getAsync = async function (
    url,
    headers = { "Content-type": "application/json; charset=UTF-8" }
  ) {
    try {
      const request = await fetch(url, {
        method: "GET",
        headers: headers,
        mode: "cors",
        cache: "no-cache",
        credentials: "same-origin",
      });
      const status = await request.status;
      if ([404].includes(status))
        return { hasError: true, message: `${url} not found.`, data: null };

      var response = await request.json();

      return getApiResponse(response);
    } catch (error) {
      return { hasError: true, message: error.message, data: null };
    }
  };

  window.post = function (
    url,
    payload,
    successCallback,
    errorCallback,
    headers = { "Content-type": "application/json; charset=UTF-8" }
  ) {
    fetch(url, {
      method: "POST",
      body: JSON.stringify(payload),
      headers: headers,
    })
      .then((response) => response.json())
      .then(function (json) {
        successCallback(json);
      })
      .catch(function (error) {
        errorCallback(error);
      });
  };

  window.put = function (
    url,
    payload,
    successCallback,
    errorCallback,
    headers = { "Content-type": "application/json; charset=UTF-8" }
  ) {
    fetch(url, {
      method: "PUT",
      body: JSON.stringify(payload),
      headers: headers,
    })
      .then((response) => response.json())
      .then(function (json) {
        successCallback(json);
      })
      .catch(function (error) {
        errorCallback(error);
      });
  };

  window.get = function (
    url,
    successCallback,
    errorCallback,
    headers = { "Content-type": "application/json; charset=UTF-8" }
  ) {
    //console.log(url);
    fetch(url, {
      method: "GET",
      headers: headers,
    })
      .then((response) => response.json())
      .then(function (json) {
        successCallback(json);
      })
      .catch(function (error) {
        errorCallback(error);
      });
  };

  /**
   * Displays message to the user
   *
   * @param type Determines the style applied to the message being displayed. Use e, i, w, s to represent error, info, warning and success message respectively
   * @param msg The actual message to be displayed.
   */
  $.fn.showMsg = function (type, msg) {
    this.html("");
    this.hide();

    if (msg !== null && msg !== "") {
      var msgType = "";

      switch (type) {
        case "1":
        case "e":
        case "ex":
        case "err":
        case "error":
          msgType = "danger";
          break;
        case "2":
        case "w":
        case "warn":
        case "warning":
          msgType = "warning";
          break;
        case "3":
        case "i":
        case "info":
          msgType = "info";
          break;
        case "0":
        case "s":
        case "success":
          msgType = "success";
          break;
        default:
          msgType = "default";
          break;
      }

      this.html(
        '<div class="alert alert-' +
          msgType +
          '" alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' +
          msg +
          "</div>"
      );
      this.show();
    }

    return this;
  };

  window.sConfirm = function (
    icon,
    title,
    message,
    yesCallback,
    noCallback = undefined
  ) {
    Swal.fire({
      title: title,
      text: message,
      icon:
        icon == "i"
          ? "info"
          : icon == "s"
          ? "success"
          : icon == "w"
          ? "warning"
          : icon == "e"
          ? "danger"
          : icon,
      showCancelButton: true,
      reverseButtons: true,
      confirmButtonText: "Yes",
      customClass: {
        confirmButton: "btn btn-primary",
      },
    }).then(function (result) {
      if (result.isConfirmed) {
        yesCallback();
        return;
      }

      if (
        noCallback !== undefined &&
        noCallback !== "undefined" &&
        noCallback !== null
      )
        noCallback();
    });
  };

  window.sAlert = function (icon, title, message, callback = undefined) {
    Swal.fire({
      title: title,
      text: message,
      icon:
        icon == "i"
          ? "info"
          : icon == "s"
          ? "success"
          : icon == "w"
          ? "warning"
          : icon == "e"
          ? "error"
          : icon,
    }).then(function () {
      if (callback) {
        callback();
      }
    });
  };
})(jQuery);
