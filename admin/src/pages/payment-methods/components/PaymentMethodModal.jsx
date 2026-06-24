import { useEffect, useState } from "react";
import {
  Modal,
  Form,
  Button,
  Spinner,
  InputGroup,
} from "react-bootstrap";

import {
  successAlert,
  errorAlert,
} from "../../utils/alert";

import {
  generateSnapToken,
  generateRedirectUrl,
} from "../../services/paymentMethodService";

export default function PaymentMethodModal({
  show,
  onHide,
  payment = null,
}) {
  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

  const [paymentNumber, setPaymentNumber] =
    useState("");

  const [snapToken, setSnapToken] =
    useState("");

  const [redirectUrl, setRedirectUrl] =
    useState("");

  const [snapLoading, setSnapLoading] =
    useState(false);

  const [
    redirectLoading,
    setRedirectLoading,
  ] = useState(false);

  /*
  |--------------------------------------------------------------------------
  | Initialize
  |--------------------------------------------------------------------------
  */

  useEffect(() => {
    if (show) {
      setPaymentNumber(
        payment?.payment_number || ""
      );

      setSnapToken("");

      setRedirectUrl("");
    }
  }, [show, payment]);

  /*
  |--------------------------------------------------------------------------
  | Helpers
  |--------------------------------------------------------------------------
  */

  const copyToClipboard = async (
    text
  ) => {
    if (!text) return;

    try {
      await navigator.clipboard.writeText(
        text
      );

      successAlert(
        "Berhasil",
        "Berhasil disalin ke clipboard."
      );
    } catch (error) {
      errorAlert(
        "Gagal",
        "Tidak dapat menyalin teks."
      );
    }
  };

  const openPaymentPage = () => {
    if (!redirectUrl) return;

    window.open(
      redirectUrl,
      "_blank",
      "noopener,noreferrer"
    );
  };

  /*
  |--------------------------------------------------------------------------
  | Snap Token
  |--------------------------------------------------------------------------
  */

  const handleGenerateSnapToken =
    async () => {
      if (!paymentNumber.trim()) {
        errorAlert(
          "Validasi",
          "Payment Number wajib diisi."
        );

        return;
      }

      try {
        setSnapLoading(true);

        const response =
          await generateSnapToken(
            paymentNumber.trim()
          );

        const token =
          response?.data?.data
            ?.snap_token || "";

        setSnapToken(token);

        successAlert(
          "Berhasil",
          "Snap Token berhasil dibuat."
        );
      } catch (error) {
        console.error(error);

        errorAlert(
          "Gagal",
          error.response?.data?.message ||
            "Gagal membuat Snap Token."
        );
      } finally {
        setSnapLoading(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Redirect URL
  |--------------------------------------------------------------------------
  */

  const handleGenerateRedirectUrl =
    async () => {
      if (!paymentNumber.trim()) {
        errorAlert(
          "Validasi",
          "Payment Number wajib diisi."
        );

        return;
      }

      try {
        setRedirectLoading(true);

        const response =
          await generateRedirectUrl(
            paymentNumber.trim()
          );

        const url =
          response?.data?.data
            ?.redirect_url || "";

        setRedirectUrl(url);

        successAlert(
          "Berhasil",
          "Redirect URL berhasil dibuat."
        );
      } catch (error) {
        console.error(error);

        errorAlert(
          "Gagal",
          error.response?.data?.message ||
            "Gagal membuat Redirect URL."
        );
      } finally {
        setRedirectLoading(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Close Modal
  |--------------------------------------------------------------------------
  */

  const handleClose = () => {
    setSnapToken("");

    setRedirectUrl("");

    onHide?.();
  };

  /*
  |--------------------------------------------------------------------------
  | Render
  |--------------------------------------------------------------------------
  */

  return (
    <Modal
      show={show}
      onHide={handleClose}
      centered
      size="lg"
      backdrop="static"
    >
      <Modal.Header closeButton>
        <Modal.Title>
          Midtrans Payment Tools
        </Modal.Title>
      </Modal.Header>

      <Modal.Body>

        <Form.Group className="mb-4">

          <Form.Label>
            Payment Number
          </Form.Label>

          <Form.Control
            type="text"
            placeholder="PAY-XXXXXXXX"
            value={paymentNumber}
            onChange={(e) =>
              setPaymentNumber(
                e.target.value
              )
            }
          />

          <Form.Text className="text-muted">
            Masukkan Payment Number
            untuk generate Snap Token
            atau Redirect URL.
          </Form.Text>

        </Form.Group>

        <div className="d-flex flex-wrap gap-2 mb-4">

          <Button
            variant="primary"
            onClick={
              handleGenerateSnapToken
            }
            disabled={snapLoading}
          >
            {snapLoading ? (
              <>
                <Spinner
                  animation="border"
                  size="sm"
                  className="me-2"
                />

                Generating...
              </>
            ) : (
              "Generate Snap Token"
            )}
          </Button>

          <Button
            variant="success"
            onClick={
              handleGenerateRedirectUrl
            }
            disabled={redirectLoading}
          >
            {redirectLoading ? (
              <>
                <Spinner
                  animation="border"
                  size="sm"
                  className="me-2"
                />

                Generating...
              </>
            ) : (
              "Generate Redirect URL"
            )}
          </Button>

        </div>

        {snapToken && (
          <div className="mb-4">

            <Form.Label>
              Snap Token
            </Form.Label>

            <InputGroup>

              <Form.Control
                value={snapToken}
                readOnly
              />

              <Button
                variant="outline-secondary"
                onClick={() =>
                  copyToClipboard(
                    snapToken
                  )
                }
              >
                Copy
              </Button>

            </InputGroup>

          </div>
        )}

        {redirectUrl && (
          <div>

            <Form.Label>
              Redirect URL
            </Form.Label>

            <InputGroup>

              <Form.Control
                value={redirectUrl}
                readOnly
              />

              <Button
                variant="outline-secondary"
                onClick={() =>
                  copyToClipboard(
                    redirectUrl
                  )
                }
              >
                Copy
              </Button>

            </InputGroup>

            <div className="mt-3">

              <Button
                variant="warning"
                onClick={
                  openPaymentPage
                }
              >
                Open Payment Page
              </Button>

            </div>

          </div>
        )}

      </Modal.Body>

      <Modal.Footer>

        <Button
          variant="secondary"
          onClick={handleClose}
        >
          Tutup
        </Button>

      </Modal.Footer>

    </Modal>
  );
}