//
//  ViewController.swift
//  SliderColor
//
//  Created by KEISUKE.Yamaguchi on 2015/12/20.
//  Copyright © 2015年 mycompany. All rights reserved.
//

import UIKit

class ViewController: UIViewController {

    @IBOutlet weak var label1: UILabel!
    @IBOutlet weak var labelR: UILabel!
    @IBOutlet weak var labelG: UILabel!
    @IBOutlet weak var labelB: UILabel!
    
    @IBOutlet weak var sliderR: UISlider!
    @IBOutlet weak var sliderG: UISlider!
    @IBOutlet weak var sliderB: UISlider!

    
    @IBAction func sliderRChanged(sender: UISlider){
        labelR.text = "R = \(sliderR.value)"
        label1.backgroundColor = UIColor(red: CGFloat(sliderR.value),green: CGFloat(sliderG.value),blue: CGFloat(sliderB.value),alpha: 1.0)
    }
    @IBAction func sliderGChanged(sender: UISlider) {
        labelG.text = "G = \(sliderG.value)"
        label1.backgroundColor = UIColor(red: CGFloat(sliderR.value),green: CGFloat(sliderG.value),blue: CGFloat(sliderB.value),alpha: 1.0)
    }
    @IBAction func sliderBChanged(sender: UISlider) {
        labelB.text = "B = \(sliderB.value)"
        label1.backgroundColor = UIColor(red: CGFloat(sliderR.value),green: CGFloat(sliderG.value),blue: CGFloat(sliderB.value),alpha: 1.0)
    }
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
        label1.backgroundColor = UIColor(red: 0.5,green: 0.5,blue: 0.5,alpha: 1.0)
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }


}

