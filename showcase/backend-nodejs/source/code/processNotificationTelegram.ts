/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */

import * as iot from "./iotMsg";

export default class ProcessNotificationTelegram {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;
  telegramBot: any; // telegrambot unfortunately does not have a typescript definition file

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages | null, telegramBot) {
    this.receiver = receiver;
    this.sender = sender;
    this.telegramBot = telegramBot;

    receiver.startConsumer((msg) => {
      this.processNotification(msg);
    });
  }

  private processNotification(notification: iot.AlertNotification) {
    // haal alarmregels
    if (notification.kanaal.toLowerCase() === "telegram") {
      this.sendNotification(notification);
    }
  }

  private sendNotification(notification: iot.AlertNotification) {
    var message = {
      text: notification.meldingtekst,
      chatId: notification.p1 || process.env.SHOWCASE_TELEGRAM_CHAT_ID || "-1001097331998",
    };
    this.telegramBot.sendMessage(message.chatId, message.text)
      .then(() => {
        if (this.sender) {
          this.sender.send(message);
        }
      })
      .catch((err) => {
        console.log("error sending telegram message: " + err.message);
      });
  }
}
