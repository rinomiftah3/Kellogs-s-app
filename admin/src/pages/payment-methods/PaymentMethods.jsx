import { useEffect, useState } from "react";
import {
  Card,
  Form,
  Row,
  Col,
  Table,
  Spinner,
  Badge,
} from "react-bootstrap";

import { errorAlert } from "../../utils/alert";

import {
  getConfiguration,
  getGateways,
  getMethods,
} from "../../services/paymentMethodService";

import PaymentMethodForm from "../../components/payments/PaymentMethodForm";

export default function PaymentMethod() {
  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

  const [loading, setLoading] = useState(true);

  const [configLoading, setConfigLoading] =
    useState(false);

  const [gatewayLoading, setGatewayLoading] =
    useState(false);

  const [methodLoading, setMethodLoading] =
    useState(false);

  const [configuration, setConfiguration] =
    useState({});

  const [gateways, setGateways] =
    useState([]);

  const [selectedGateway, setSelectedGateway] =
    useState("");

  const [methods, setMethods] =
    useState([]);

  /*
  |--------------------------------------------------------------------------
  | Helpers
  |--------------------------------------------------------------------------
  */

  const maskServerKey = (value) => {
    if (!value) return "-";

    if (value.length <= 10) {
      return "********";
    }

    return (
      value.substring(0, 6) +
      "****************" +
      value.substring(value.length - 4)
    );
  };

  /*
  |--------------------------------------------------------------------------
  | Load Configuration
  |--------------------------------------------------------------------------
  */

  const fetchConfiguration = async () => {
    try {
      setConfigLoading(true);

      const response =
        await getConfiguration();

      setConfiguration(
        response.data?.data || {}
      );
    } catch (error) {
      console.error(error);

      errorAlert(
        "Gagal",
        error.response?.data?.message ||
          "Gagal mengambil konfigurasi Midtrans."
      );
    } finally {
      setConfigLoading(false);
    }
  };

  /*
  |--------------------------------------------------------------------------
  | Load Gateways
  |--------------------------------------------------------------------------
  */

  const fetchGateways = async () => {
    try {
      setGatewayLoading(true);

      const response =
        await getGateways();

      const data =
        response.data?.data || [];

      setGateways(data);

      if (data.length > 0) {
        setSelectedGateway(data[0]);
      }
    } catch (error) {
      console.error(error);

      errorAlert(
        "Gagal",
        error.response?.data?.message ||
          "Gagal mengambil gateway pembayaran."
      );
    } finally {
      setGatewayLoading(false);
    }
  };

  /*
  |--------------------------------------------------------------------------
  | Load Methods
  |--------------------------------------------------------------------------
  */

  const fetchMethods = async (
    gateway
  ) => {
    if (!gateway) return;

    try {
      setMethodLoading(true);

      const response =
        await getMethods(gateway);

      setMethods(
        response.data?.data?.methods || []
      );
    } catch (error) {
      console.error(error);

      errorAlert(
        "Gagal",
        error.response?.data?.message ||
          "Gagal mengambil metode pembayaran."
      );
    } finally {
      setMethodLoading(false);
    }
  };

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {
    const initialize = async () => {
      setLoading(true);

      await Promise.all([
        fetchConfiguration(),
        fetchGateways(),
      ]);

      setLoading(false);
    };

    initialize();
  }, []);

  /*
  |--------------------------------------------------------------------------
  | Gateway Changed
  |--------------------------------------------------------------------------
  */

  useEffect(() => {
    if (selectedGateway) {
      fetchMethods(selectedGateway);
    }
  }, [selectedGateway]);

  /*
  |--------------------------------------------------------------------------
  | Loading
  |--------------------------------------------------------------------------
  */

  if (loading) {
    return (
      <div className="text-center py-5">
        <Spinner animation="border" />

        <p className="mt-3">
          Memuat Payment Method...
        </p>
      </div>
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Render
  |--------------------------------------------------------------------------
  */

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h3 className="mb-0">
          Payment Method
        </h3>

        <Badge bg="success">
          Midtrans Integration
        </Badge>
      </div>

      {/* Configuration */}

      <Card className="mb-4 shadow-sm">
        <Card.Header>
          <strong>
            Midtrans Configuration
          </strong>
        </Card.Header>

        <Card.Body>
          {configLoading ? (
            <Spinner animation="border" />
          ) : (
            <Table bordered hover>
              <tbody>
                <tr>
                  <th width="30%">
                    Server Key
                  </th>

                  <td>
                    {maskServerKey(
                      configuration.server_key
                    )}
                  </td>
                </tr>

                <tr>
                  <th>Client Key</th>

                  <td>
                    {configuration.client_key ||
                      "-"}
                  </td>
                </tr>

                <tr>
                  <th>Production</th>

                  <td>
                    {configuration.is_production ? (
                      <Badge bg="danger">
                        Yes
                      </Badge>
                    ) : (
                      <Badge bg="success">
                        No
                      </Badge>
                    )}
                  </td>
                </tr>

                <tr>
                  <th>Sanitized</th>

                  <td>
                    {configuration.is_sanitized ? (
                      <Badge bg="success">
                        Enabled
                      </Badge>
                    ) : (
                      <Badge bg="secondary">
                        Disabled
                      </Badge>
                    )}
                  </td>
                </tr>

                <tr>
                  <th>3DS</th>

                  <td>
                    {configuration.is_3ds ? (
                      <Badge bg="success">
                        Enabled
                      </Badge>
                    ) : (
                      <Badge bg="secondary">
                        Disabled
                      </Badge>
                    )}
                  </td>
                </tr>
              </tbody>
            </Table>
          )}
        </Card.Body>
      </Card>

      <Row>
        <Col lg={4}>
          <Card className="shadow-sm mb-4">
            <Card.Header>
              <strong>
                Available Gateways
              </strong>
            </Card.Header>

            <Card.Body>
              {gatewayLoading ? (
                <Spinner animation="border" />
              ) : (
                <Form.Select
                  value={selectedGateway}
                  onChange={(e) =>
                    setSelectedGateway(
                      e.target.value
                    )
                  }
                >
                  {gateways.map(
                    (gateway) => (
                      <option
                        key={gateway}
                        value={gateway}
                      >
                        {gateway.toUpperCase()}
                      </option>
                    )
                  )}
                </Form.Select>
              )}
            </Card.Body>
          </Card>
        </Col>

        <Col lg={8}>
          <Card className="shadow-sm mb-4">
            <Card.Header>
              <strong>
                Available Methods
              </strong>
            </Card.Header>

            <Card.Body>
              {methodLoading ? (
                <Spinner animation="border" />
              ) : (
                <div className="d-flex flex-wrap gap-2">
                  {methods.length > 0 ? (
                    methods.map(
                      (method) => (
                        <Badge
                          key={method}
                          bg="primary"
                          className="p-2"
                        >
                          {method}
                        </Badge>
                      )
                    )
                  ) : (
                    <span>
                      Tidak ada metode.
                    </span>
                  )}
                </div>
              )}
            </Card.Body>
          </Card>
        </Col>
      </Row>

      {/* Midtrans Tools */}

      <PaymentMethodForm />
    </div>
  );
}