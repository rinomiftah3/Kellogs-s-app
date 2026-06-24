import api from "../api/axios";
/*
|--------------------------------------------------------------------------
| Payment Method Service
|--------------------------------------------------------------------------
|
| Backend:
| GET    /payment-methods/gateways
| GET    /payment-methods/methods/{gateway}
| POST   /payment-methods/{payment}/snap-token
| GET    /payment-methods/{payment}/redirect-url
| GET    /payment-methods/configuration
|
| Note:
| payment = payment_number
|
*/

const BASE_URL = "/payment-methods";

/*
|--------------------------------------------------------------------------
| Get Available Gateways
|--------------------------------------------------------------------------
*/

export const getGateways = async () => {
    const response = await api.get(
        `${BASE_URL}/gateways`
    );

    return response.data;
};

/*
|--------------------------------------------------------------------------
| Get Available Methods By Gateway
|--------------------------------------------------------------------------
*/

export const getMethods = async (gateway) => {
    const response = await api.get(
        `${BASE_URL}/methods/${gateway}`
    );

    return response.data;
};

/*
|--------------------------------------------------------------------------
| Generate Midtrans Snap Token
|--------------------------------------------------------------------------
|
| paymentNumber:
| PAY-20260618123000-ABC123
|
*/

export const getSnapToken = async (
    paymentNumber
) => {
    const response = await api.post(
        `${BASE_URL}/${paymentNumber}/snap-token`
    );

    return response.data;
};

/*
|--------------------------------------------------------------------------
| Generate Midtrans Redirect URL
|--------------------------------------------------------------------------
*/

export const getRedirectUrl = async (
    paymentNumber
) => {
    const response = await api.get(
        `${BASE_URL}/${paymentNumber}/redirect-url`
    );

    return response.data;
};

/*
|--------------------------------------------------------------------------
| Get Midtrans Configuration
|--------------------------------------------------------------------------
|
| Digunakan untuk kebutuhan admin/debug.
|
*/

export const getConfiguration = async () => {
    const response = await api.get(
        `${BASE_URL}/configuration`
    );

    return response.data;
};

/*
|--------------------------------------------------------------------------
| Default Export
|--------------------------------------------------------------------------
*/

const paymentMethodService = {
    getGateways,
    getMethods,
    getSnapToken,
    getRedirectUrl,
    getConfiguration,
};

export default paymentMethodService;