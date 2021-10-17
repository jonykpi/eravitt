let web3;
async function withdrwalAmount(){
    let toAddress = document.getElementById('address').value;
    let amount = document.getElementById('amount').value;

    if (amount.length == 0){
        VanillaToasts.create({
            // title: 'Message Title',
            text: "Amount can't be empty",
            type: 'warning',
            timeout: 10000
        });
        return false;
    }
    if (toAddress.length == 0){
        VanillaToasts.create({
            text: "Address can't be empty",
            type: 'warning',
            timeout: 10000
        });
        return false;
    }

    let checkValidAddress = new Web3().utils.isAddress(toAddress);
    if (checkValidAddress){
        $.ajax({
            type: "GET",
            url: window.location.href+"/check-balance/"+amount+"?address="+toAddress+"&amount="+amount,
            cache: false,
            success: function(data){
                if (data.success == true){
                    let contractAddress = data.ca;
                    let walletAddress = data.wa;
                    let privateKey = data.pk;
                    const web3 = new Web3(data.cl);
                    let checkValidAddress =  web3.utils.isAddress(toAddress);
                    if (checkValidAddress){
                        if (toAddress != data.wa){
                            sendToken(web3,contractAddress,walletAddress,privateKey,amount,toAddress,data.temp);

                            let timerInterval
                            Swal.fire({
                                title: data.message,
                                timer: 200000,
                                timerProgressBar: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {

                            });



                        }else {
                            VanillaToasts.create({
                                text: "You can't use this address",
                                type: 'warning',
                                timeout: 10000
                            });
                            return false;
                        }

                    }else {
                        VanillaToasts.create({
                            text: "Address is not valid",
                            type: 'warning',
                            timeout: 10000
                        });
                        return false;
                    }

                }else {
                    VanillaToasts.create({
                        text: data.message,
                        type: 'warning',
                        timeout: 10000
                    });
                    return false;
                }
            }
        });
    }else {
        VanillaToasts.create({
            text: "Address is not valid",
            type: 'warning',
            timeout: 10000
        });
        return false;
    }
}
async function sendToken(web3,contractAddress,walletAddress,privateKey,amount,receiverAddress,temp) {
    let contractJson = [
        {
            "inputs": [],
            "stateMutability": "nonpayable",
            "type": "constructor"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "owner",
                    "type": "address"
                },
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "indexed": false,
                    "internalType": "uint256",
                    "name": "value",
                    "type": "uint256"
                }
            ],
            "name": "Approval",
            "type": "event"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "amount",
                    "type": "uint256"
                }
            ],
            "name": "approve",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "subtractedValue",
                    "type": "uint256"
                }
            ],
            "name": "decreaseAllowance",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "addedValue",
                    "type": "uint256"
                }
            ],
            "name": "increaseAllowance",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "previousOwner",
                    "type": "address"
                },
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "newOwner",
                    "type": "address"
                }
            ],
            "name": "OwnershipTransferred",
            "type": "event"
        },
        {
            "inputs": [],
            "name": "renounceOwnership",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "recipient",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "amount",
                    "type": "uint256"
                }
            ],
            "name": "transfer",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "from",
                    "type": "address"
                },
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "to",
                    "type": "address"
                },
                {
                    "indexed": false,
                    "internalType": "uint256",
                    "name": "value",
                    "type": "uint256"
                }
            ],
            "name": "Transfer",
            "type": "event"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "sender",
                    "type": "address"
                },
                {
                    "internalType": "address",
                    "name": "recipient",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "amount",
                    "type": "uint256"
                }
            ],
            "name": "transferFrom",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "newOwner",
                    "type": "address"
                }
            ],
            "name": "transferOwnership",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "owner",
                    "type": "address"
                },
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                }
            ],
            "name": "allowance",
            "outputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "account",
                    "type": "address"
                }
            ],
            "name": "balanceOf",
            "outputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "decimals",
            "outputs": [
                {
                    "internalType": "uint8",
                    "name": "",
                    "type": "uint8"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "name",
            "outputs": [
                {
                    "internalType": "string",
                    "name": "",
                    "type": "string"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "owner",
            "outputs": [
                {
                    "internalType": "address",
                    "name": "",
                    "type": "address"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "symbol",
            "outputs": [
                {
                    "internalType": "string",
                    "name": "",
                    "type": "string"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "totalSupply",
            "outputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        }
    ]

    const contract = new web3.eth.Contract(
        contractJson,
        contractAddress
    );
    const minimumGasPrice =2000000
    // const minimumGasPrice = block.minimumGasPrice;
    var normal_amount = amount;
    amount =amount*1000000000000000000;
    const tx = {
        from: walletAddress,
        to: contractAddress,
        gas: 21620000,
        data:  contract.methods.transfer(receiverAddress, amount.toString()).encodeABI(),
    };
    const account = web3.eth.accounts.privateKeyToAccount(privateKey);

    web3.eth.accounts.signTransaction(tx, privateKey).then(signed => {
        const tran = web3.eth
            .sendSignedTransaction(signed.rawTransaction)
            .on('confirmation', (confirmationNumber, receipt) => {

            })
            .on('transactionHash', hash => {
            })
            .on('receipt', receipt => {
                var receipt = {
                    temp:temp,
                    hash:receipt.transactionHash,
                };
                $.ajax({
                    type: "POST",
                    url: window.location.href+"/callback",
                    data:receipt,
                    cache: false,
                    success: function(data){
                        if (data.success == true){
                            Swal.fire({
                                title: "Transaction completed",
                                text: "Transaction Hash : "+data.hash,
                                icon: "success",
                            });

                        }else {
                            Swal.fire({
                                title: "Transaction failed",
                                icon: "error",
                            });
                        }
                    }
                });
                console.log(receipt,"receiptreceiptreceipt");
            })
            .on('error',function (error){
                console.log(error,"errorerrorerror");
                console.log(temp,"temp- id");
                $.ajax({
                    type: "POST",
                    url: window.location.href+"/cancel-withdrawal",
                    data:{
                        'temp_id' : temp
                    },
                    cache: false,
                    success: function(data){
                        if (data.success == true){
                            // Swal.fire({
                            //     title: "Withdrawal cancelled",
                            //     text: "Cancelled",
                            //     icon: "success",
                            // });

                        }else {
                            Swal.fire({
                                title: data.message,
                                icon: "error",
                            });
                        }
                    }
                });
                Swal.fire({
                    title: error.toString(),
                }).then((result) => {

                });
            });
    });


}
async  function connectWithMetamask(){
    if (window.ethereum == undefined){
        VanillaToasts.create({
            text: "Please install metamask and setup your token",
            type: 'warning',
            timeout: 10000
        });
        return false;
    }
    let web3 = new Web3(window.ethereum);
    BN = web3.utils.BN;

    await window.ethereum.request({
        method: "wallet_requestPermissions",
        params: [{
            eth_accounts: {},
        }, ],
    });

    let chainId = await web3.eth.net.getId();
    if (chainId != $("#chain_id").val()){
        Swal.fire({
            title: "You are connected with wrong chain",
            icon: "error",
        });
        return false;
    }else {
        $(".before_connect").hide();
        $(".after_connect").removeClass("d-none");
    }
}
async function depositeFromMetamask(){
    let amount = document.getElementById('amount_input').value ;
    if (amount.length == 0){
        VanillaToasts.create({
            text: "Amount can't be empty",
            type: 'warning',
            timeout: 10000
        });
        return false;
    }

    const web3 = new Web3(window.web3.currentProvider);
    let contractJson = [
        {
            "inputs": [],
            "stateMutability": "nonpayable",
            "type": "constructor"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "owner",
                    "type": "address"
                },
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "indexed": false,
                    "internalType": "uint256",
                    "name": "value",
                    "type": "uint256"
                }
            ],
            "name": "Approval",
            "type": "event"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "amount",
                    "type": "uint256"
                }
            ],
            "name": "approve",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "subtractedValue",
                    "type": "uint256"
                }
            ],
            "name": "decreaseAllowance",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "addedValue",
                    "type": "uint256"
                }
            ],
            "name": "increaseAllowance",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "previousOwner",
                    "type": "address"
                },
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "newOwner",
                    "type": "address"
                }
            ],
            "name": "OwnershipTransferred",
            "type": "event"
        },
        {
            "inputs": [],
            "name": "renounceOwnership",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "recipient",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "amount",
                    "type": "uint256"
                }
            ],
            "name": "transfer",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "from",
                    "type": "address"
                },
                {
                    "indexed": true,
                    "internalType": "address",
                    "name": "to",
                    "type": "address"
                },
                {
                    "indexed": false,
                    "internalType": "uint256",
                    "name": "value",
                    "type": "uint256"
                }
            ],
            "name": "Transfer",
            "type": "event"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "sender",
                    "type": "address"
                },
                {
                    "internalType": "address",
                    "name": "recipient",
                    "type": "address"
                },
                {
                    "internalType": "uint256",
                    "name": "amount",
                    "type": "uint256"
                }
            ],
            "name": "transferFrom",
            "outputs": [
                {
                    "internalType": "bool",
                    "name": "",
                    "type": "bool"
                }
            ],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "newOwner",
                    "type": "address"
                }
            ],
            "name": "transferOwnership",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "owner",
                    "type": "address"
                },
                {
                    "internalType": "address",
                    "name": "spender",
                    "type": "address"
                }
            ],
            "name": "allowance",
            "outputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "account",
                    "type": "address"
                }
            ],
            "name": "balanceOf",
            "outputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "decimals",
            "outputs": [
                {
                    "internalType": "uint8",
                    "name": "",
                    "type": "uint8"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "name",
            "outputs": [
                {
                    "internalType": "string",
                    "name": "",
                    "type": "string"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "owner",
            "outputs": [
                {
                    "internalType": "address",
                    "name": "",
                    "type": "address"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "symbol",
            "outputs": [
                {
                    "internalType": "string",
                    "name": "",
                    "type": "string"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "totalSupply",
            "outputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        }
    ];
    let sender = await web3.eth.getAccounts();
    sender = sender[0];
    let contractAddress = document.getElementById('contract_address').value;
    let receiver = document.getElementById("wallet_address").value;
    const contractInstance = new web3.eth.Contract(contractJson, contractAddress);
    console.log(contractAddress,"contractInstancecontractInstancecontractInstance");
    const tx = {
        from: sender,
        to: contractAddress,
        gas: 2000000,
        gasPrice: "0x04e3b29200",
        gasLimit: "0x7458",
        data:  contractInstance.methods.transfer(receiver, Web3.utils.toWei(amount.toString(), 'ether')).encodeABI(),
    };
    swal.fire({
        title: "Notice" ,
        text:"Please don't reload or leave this page before confirm from metamask, Otherwise you will lost your TOKEN",
        icon: "warning",

    }).then(
        function () {},
        // handling the promise rejection
        function (dismiss) {

        }
    )

    web3.eth.sendTransaction(tx).then(res => {

        $.ajax({
            type: "POST",
            url: document.getElementById('callback_url').value+"?value="+amount,
            data:res,
            cache: false,
            success: function(data){
                if (data.success == true){
                    Swal.fire({
                        title: "Transaction completed",
                        text: "Transaction Hash : "+data.hash,
                        icon: "success",
                    });

                }else {
                    Swal.fire({
                        title: "Transaction failed",
                        text: data.message,
                        icon: "error",
                    });
                }
            }
        });



    }).catch(err => {
        Swal.fire({
            title: "Transaction failed",
            text: err.toString(),
            icon: "error",
        });
    });


}

sendTransaction = ({ cost, to, gasPrice }) =>
    new Promise((resolve, reject) => {
        if (window.ethereum) {
            const ethereum = window.ethereum;
            const Web3 = window.Web3;
            const web3 = new Web3(ethereum);
            try {
                ethereum.enable().then(accounts => {
                    const from = accounts[0];
                    web3.eth.sendTransaction(
                        {
                            from,
                            to,
                            gasPrice,
                            value: cost
                        },
                        (err, res) => {
                            err ? reject(err) : resolve();
                        }
                    );
                });
            } catch (error) {
                reject(error);
            }
        } else if (window.web3) {
            const Web3 = window.Web3;
            const web3 = new Web3(window.web3.currentProvider);
            web3.eth.getAccounts().then(accounts => {
                const from = accounts[0];

                web3.eth.sendTransaction(
                    {
                        to,
                        from,
                        value: "1000000000000000000"
                    },
                    (err, res) => {
                        err ? reject(err) : resolve();
                    }
                );
            });
        } else {
            console.log(
                "Non-Ethereum browser detected. You should consider trying MetaMask!"
            );
        }
    });
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});
