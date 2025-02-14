# Tucker
Like the chimera of Nina Tucker, PHP based local enumeration of windows systems.

# Theory

Based on several situations i have encountered while doing post exploitation activities, most published tools are so signatured that even making a request out to the repo causes a detection. Although these current tools such as, inveigh, responder, pingCastle, etc, are amazing and very powerful (there is no shade thrown here, i have used these tools successfully before.) More often than not, actually getting them to run on a domain joined, actively monitored system are near impossible. 

Thats where tucker comes in. Tucker is written in pure PHP, which means no external dependencies (and I intend to keep it that way.). The goal of this is to attempt to recreate at least basic local recon during an assumed breach assessment. There is still ALOT of work to do, and this tool is by no means complete just yet, but its a work in progress. 

# How to use

With development of things like ![BYOSI](https://github.com/oldkingcone/BYOSI) it is possible to deploy a portal version of PHP to a windows system, and avoid the EDR/AV entirely, only things like telemetry are picked up by EDR systems.

# What this tool does to attempt to avoid that

This tool is designed to be run on the local host using the following syntax: `php -S 127.0.0.1:8000` which serves the PHP script over port 8000 bound to the localhost (127.0.0.1) and all operations are done from within the browser, which means no powershell execution, no batch commands, no VBS, no windows native scripting is touched, so at least in theory, no telemetry.

# Disclaimer

This software is released as-is, and I or any of the contributors are not responsible for the misuse of this product. This is released for legitimate penetration testing activities and for educational value. Please know the laws in the country you reside in before using this product, and/or seek explicit approval from any system owner/organization before running this script on a system you do not own. 
