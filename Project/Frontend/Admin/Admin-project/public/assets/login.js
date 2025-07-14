function encryptAndSubmit() {
    const publicKey = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2AO7zvFQUgyeQx7Ey7e/
Hb5NV88V4nhMnPLi6Ei3gqJ0RViDWRpDTndjxP+pDT6r45cJCNCR8OJDE9RZbgu+
/P99wbP3viM1DurF27TBIisnubre/5b0xcP9TxHX6LOiIZV/DHwXEZJ4ciZOiJEV
Oa7+lh4UlmYGRTfVrw3Ahb9ifgh0hyvsnFXQpjXOuwZkPdcbS6bETdlq434U7xiO
X522WbrrWb6uV0NoEr9lcEV/3GRDO/LFcn+DFUH6byAqAgxYnW5LpElSSJvMTc92
sXTJwCLaI9y523ADO57fuwcHcoZKY3TDv38TwIg5e5VctX3WFL3GH2gvN4T+t/yD
fwIDAQAB
-----END PUBLIC KEY-----`;

    const encryptor = new JSEncrypt();
    encryptor.setPublicKey(publicKey);

    //const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    //const encryptedUsername = encryptor.encrypt(username);
    const encryptedEmail = encryptor.encrypt(email);
    const encryptedPassword = encryptor.encrypt(password);

    if (encryptedEmail && encryptedPassword) {
       // document.getElementById('username').value = encryptedUsername;
        document.getElementById('email').value = encryptedEmail;
        document.getElementById('password').value = encryptedPassword;

        document.getElementById('formAuthentication').submit(); // Submit the form after encryption
    } else {
        alert("Encryption failed. Please try again.");
    }
}