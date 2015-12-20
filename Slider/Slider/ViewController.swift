//
//  ViewController.swift
//  Slider
//
//  Created by KEISUKE.Yamaguchi on 2015/12/20.
//  Copyright © 2015年 mycompany. All rights reserved.
//

import UIKit

class ViewController: UIViewController {

    @IBOutlet weak var label: UILabel!
    @IBAction func showValue(sender: UISlider) {
        label.text = "\(sender.value)"
    }
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }


}

